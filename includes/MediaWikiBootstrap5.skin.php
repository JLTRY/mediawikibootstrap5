<?php
/**
 * MediaWikiBootstrap Skin
 * 
 * @file
 * @ingroup Skins
 * @author JL TRYOEN
 * licence http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

use MediaWiki\MediaWikiServices;



/**
 * SkinTemplate class for the BootstrapMediaWiki skin
 *
 * @ingroup Skins
 */
class SkinMediaWikiBootstrap5  extends SkinMustache {
	
	public function __construct( $options = [] ) {		
		$options['scripts'] = [ 'skins.mediawikibootstrap5.js' ];
		$options['styles'] = [ 'skins.mediawikibootstrap5' ];
		$options['template'] = 'skin';
		unset( $options['link'] );		
		parent::__construct( $options );
	}
	
	function getPageRawText($title) {
		$pageTitle = Title::newFromText($title);
		if(!$pageTitle->exists()) {
		  return null;
		} else {
		  $article = new Article($pageTitle);
		  return $article->getPage()->getContent()->getNativeData();
		}
	}
		
	private function buildContentActionUrls( $content_navigation ) {
		// content_actions has been replaced with content_navigation for backwards
		// compatibility and also for skins that just want simple tabs content_actions
		// is now built by flattening the content_navigation arrays into one

		$content_actions = [];

		foreach ( $content_navigation as $navigation => $links ) {
			foreach ( $links as $key => $value ) {
				if ( isset( $value['redundant'] ) && $value['redundant'] ) {
					// Redundant tabs are dropped from content_actions
					continue;
				}

				// content_actions used to have ids built using the "ca-$key" pattern
				// so the xmlID based id is much closer to the actual $key that we want
				// for that reason we'll just strip out the ca- if present and use
				// the latter potion of the "id" as the $key
				if ( isset( $value['id'] ) && substr( $value['id'], 0, 3 ) == 'ca-' ) {
					$key = substr( $value['id'], 3 );
				}

				if ( isset( $content_actions[$key] ) ) {
					wfDebug( __METHOD__ . ": Found a duplicate key for $key while flattening " .
						"content_navigation into content_actions." );
					continue;
				}
				$value['key'] = $key;
				$content_actions[$key] = $value;
			}
		}

		return array_values($content_actions);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTemplateData(): array {
		global $wgFooterTexts;
		$skin = $this;
		$out = $skin->getOutput();
		$title = $out->getTitle();
		$parentData = parent::getTemplateData();
		$content_actions = $this->buildContentActionUrls($this->buildContentNavigationUrls());
		$personal_urls = array_values($this->injectLegacyMenusIntoPersonalTools($this->buildContentNavigationUrls()));
		$this->data['nav_urls'] = $this->buildNavUrls();
		$this->data['notspecialpage'] = !$title->isSpecialPage();
		// Naming conventions for Mustache parameters.
		//
		// Value type (first segment):
		// - Prefix "is" or "has" for boolean values.
		// - Prefix "msg-" for interface message text.
		// - Prefix "html-" for raw HTML.
		// - Prefix "data-" for an array of template parameters that should be passed directly
		//   to a template partial.
		// - Prefix "array-" for lists of any values.
		//
		// Source of value (first or second segment)
		// - Segment "page-" for data relating to the current page (e.g. Title, WikiPage, or OutputPage).
		// - Segment "hook-" for any thing generated from a hook.
		//   It should be followed by the name of the hook in hyphenated lowercase.
		//
		// Conditionally used values must use null to indicate absence (not false or '').

		$commonSkinData = array_merge( $parentData, [
			'html-connexion' => $this->connexion(),
			'html-search-box' => $this->html_search_box(),
			'html-get-user-name' => $this->getUser()->getName(),
			'html-title' => $out->getPageTitle(),
			'html-categories' => $skin->getCategories(),	
			'html-output' => $out->getHTML(),
			'is-article' => (bool)$out->isArticle(),
			'is-anon' => $this->getUser()->isAnon(),
			'is-mainpage' => $title->isMainPage(),
			'is-registered' => $this->getUser() && $this->getUser()->isRegistered(),
			'data-navbar' => $this->data_navbar(),
			'data-content-actions' => $content_actions,
			"data-texts" =>	array_values($wgFooterTexts),
			'data-personal-urls' => array_values($personal_urls),
			'data-toolbox' => $this->data_toolbox(),
			'data-footer-texts' => $this->data_footer_texts(),
			'data-footer-links' => $this->data_footer_links()
		]);
	

		return $commonSkinData;
	}

	function connexion(): string {			
		$templateParser = $this->getTemplateParser();
		$returnto = $this->getReturnToParam();
		$loginData = $this->buildLoginData( $returnto, True );
		return $templateParser->processTemplate('UserLinks__login',
				 [ 'htmlLogin' => $this->makeLink( 'login', $loginData )]);
	}
	
	

	function html_search_box()
	{
		$config = $this->getConfig();		
		$html_search_box = '<form class="pull-right" style="display:inline;" action="'.
			$config->get( 'Script' ) .
			'" id="search-form">' .
			'<input type="text" placeholder="Search" name="search" onchange="$(\'#search-form\').submit()" />
		</form>';
		return $html_search_box;
	}
	
	function getPageContent($page)
	{
		$nabvar = "";
		$titleBar = $this->getPageRawText($page);

		$data_navbar = array();
		foreach(explode("\n", $titleBar) as $line) {
			if (trim($line) == '') continue;

			if (preg_match('/^\*\s*\[\[(.+)\|(.+)\]\]/', $line, $match)) {
				$data_navbar[] = array('title'=>$match[2], 'link'=>$match[1], 'html'=>true);
			}
			elseif (preg_match('/^\*\s*\[([^ ]+) +(.+)\]/', $line, $match)) {			
				$data_navbar[] = array('title'=>$match[2], 'link'=>$match[1], 'html'=>true);
			}
			elseif (preg_match('/^\*\s*\[\[(.+)\]\]/', $line, $match)) {
				$data_navbar[] = array('title'=>$match[1], 'link'=>$match[1], 'html'=>true);
			}
			elseif (preg_match('/^\*\*\s*\[\[(.+)\|(.+)\]\]/', $line, $match)) {
				$data_navbar[count($data_navbar)-1]['sublinks'][] = array('title'=>$match[2], 'link'=>$match[1], 'html'=>true);
				$data_navbar[count($data_navbar)-1]['class'] = 'dropdown-toggle';
				$data_navbar[count($data_navbar)-1]['link'] = '#';
				$data_navbar[count($data_navbar)-1]['parent'] = true;
			}
			elseif( preg_match('/\*\*\s*\[\[(.+)\]\]/', $line, $match)) {
				$data_navbar[count($data_navbar)-1]['sublinks'][] = array('title'=>$match[1], 'link'=>$match[1], 'html'=>true);
				$data_navbar[count($data_navbar)-1]['class'] = 'dropdown-toggle';
				$data_navbar[count($data_navbar)-1]['link'] = '#';
				$data_navbar[count($data_navbar)-1]['parent'] = true;
			}
			elseif (preg_match('/^\*\s*(.+)/', $line, $match)) {
				$data_navbar[] = array('title'=>$match[1], 'html'=>true);
			}
		}	   
		return $data_navbar;  
	}	


	function data_navbar() {
		return $this->getPageContent('MediaWikiBootstrap:TitleBar');
	}


	/*************************************************************************************************/
	function data_toolbox() {
		
		$data_toolbox = array();
		if ($this->data['notspecialpage']) {
			$data_toolbox[] = [
							'id' => "t-whatlinkshere",
							'href' => htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href']),
							'msg' => $this->msg('whatlinkshere')
						  ];
		}
		if ($this->data['nav_urls']['recentchangeslinked'] ) { 
			$data_toolbox[] = [
							'id' => "t-recentchangeslinked",
							'href' => htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href']),
							'msg' => $this->msg('recentchangeslinked')
						  ];
		}
		if (isset($this->data['nav_urls']['trackbacklink'])) { 
			$data_toolbox[] = [
							'id' => "t-trackbacklink",
							'href' => htmlspecialchars($this->data['nav_urls']['trackbacklink']['href']),
							'msg' => $this->msg('trackbacklink')
						  ];
		}				
		foreach( array('contributions', 'log', 'blockip', 'emailuser', 'upload', 'specialpages') as $special ) {
			if ($this->data['nav_urls'][$special]) {
				$data_toolbox[] = [
							'id' => 't-' . $special ,
							'href' => htmlspecialchars($this->data['nav_urls'][$special]['href']),
							'msg' => $this->msg($special)
						  ];				
			}
		}
		if (!empty($this->data['nav_urls']['print']['href'])) { 
			$data_toolbox[] = [
							'id' => 't-print' ,
							'href' => htmlspecialchars($this->data['nav_urls']['print']['href']),
							'msg' => $this->msg('printableversion')
						  ];				
		}

		if (!empty($this->data['nav_urls']['permalink']['href'])) { 
			$data_toolbox[] = [
							'id' => 't-permalink' ,
							'href' => htmlspecialchars($this->data['nav_urls']['permalink']['href']),
							'msg' => $this->msg('permalink')
						  ];				
		}
		return $data_toolbox;				
	}


	
	function data_footer_links() {
		return $this->getPageContent('MediaWikiBootstrap:FooterLinks');
	}
	
	function data_footer_texts() {
		$data_row_texts = $this->getPageRawText('MediaWikiBootstrap:FooterTexts');
		$lines = array();		
		foreach(explode("\n",$data_row_texts) as $line )
		{			
			$lines[] = ["line" => $line];
		}
		return $lines;
	}
}
