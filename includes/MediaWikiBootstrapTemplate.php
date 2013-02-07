<?php
/**
 * MEdiawikiBootstrap - A MediaWiki skin based on Twitter's excellent Bootstrap CSS framework
 *
 * @Version 1.0.0
 * @Author JL Tryoen
 * @Copyright JL Tryoen 2013 - http://www.jltryoen.fr/
 * @License: GPLv2 (http://www.gnu.org/copyleft/gpl.html)
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

if ( ! function_exists ('IsJoomla')) {
	function isJoomla() {
		return false;
	}
}



/**
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */
class MediaWikiBootstrapTemplate extends BaseTemplate  {
	/**
	 * @var Cached skin object
	 */
	var $skin;



	public static function onOutputPageBodyAttributes( $out, $sk, &$bodyAttrs ) {
		$bodyAttrs['class'] .= ' site';
 		return true;
	}
	/*
		@access private
	*/
	public function retrievefooter() {
		global $wgFooterIcons;
		foreach($wgFooterIcons as $wgFooterIcon) {
			foreach ($wgFooterIcon as $key  => $value) {
				if (array_key_exists('url', $value)) {
					$this->data[$key] = sprintf("<a href=\"%s\" title=\"%s\"><img src=\"%s\"></a>",
											$value['url'],
											$value['alt'],
											$value['src']);
				}
				if (array_key_exists('txt', $value)) {
					$this->data[$key] = $value['txt'];
				}
			}
		}
	}
	/**
	* Template filter callback for Bootstrap skin.
	* Takes an associative array of data set from a SkinTemplate-based
	* class, and a wrapper for MediaWiki's localization database, and
	* outputs a formatted page.
	*
	* @access private
	*/
	function execute() {
		global $wgOut,$wgUser, $wgSitename, $wgCopyrightLink, $wgCopyright, $wgBootstrap, $wgArticlePath, $wgGoogleAnalyticsID, $wgSiteCSS;
		
		$this->skin = $this->data['skin'];
		$this->retrievefooter();
		// Suppress warnings to prevent notices about missing indexes in $this->data
		//wfSuppressWarnings();
		$this->html('headelement');
?><!DOCTYPE html>
<html xml:lang="<?php $this->text('lang') ?>" lang="<?php $this->text('lang') ?>" dir="<?php $this->text('dir') ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="<?php $this->text('mimetype') ?>; charset=<?php $this->text('charset') ?>" />
	<title><?php $this->text('pagetitle') ?></title> 
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

</head>
<!-- Body -->
<body class="<?php  Sanitizer::escapeClass('page-' . $this->data['title'])?>">
	<div class="body">
		<div class="container">
			<div class="header">
				<div class="header-inner">
					<table>
					<tr>
						<td rowspan="2">
							<?php $this->logoBox(); ?>
						</td>
						<td>
							<?php if (IsJoomla()==false) {
								$this->Connexion();
							}
							else	{
								echo "J";
							}
							?>
						</td>
					</tr>
					<tr>
						<td>
							<?php $this->search_box(); ?>
						</td>
					</tr>
					</table>
				</div>
			</div>
			<div class="container">
				<div class="navbar navbar-expand-lg navbar-light bg-faded">
					<button class="navbar-toggler ml-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
				</div>
				<div class="navbar navbar-expand-lg navbar-light bg-faded navbar-collapse" id="navbarSupportedContent" style="position:relative;margin-left:-15px;">
					<ul class="menu navbar-nav me-auto" style="margin-left:-10px;">
						<?php $this->navbar(); ?>
						<?php
							if ($wgUser->isRegistered()) { 
						?>
						<?php	
								$this->personal_urls($wgUser);
								$this->content_actions();
								$this->toolbox();

							}							
						?>
					</ul>  

				</div>		
			</div>
			<?php $this->contentBox(); ?>
		</div><!-- container -->
	</div><!-- body -->
	<div class="footer">
		  <div class="container">
			<?php $this->footerBox(); ?>
			<br/><br/>
		  </div><!-- container -->
		</div><!--footer -->
		<?php $this->html('bottomscripts'); ?>
		<?php $this->html('reporttime') ?>
	</div><!--footer -->
</body>

</html>
<?php
	}


/*************************************************************************************************/
	function getPageRawText($title) {
		$pageTitle = Title::newFromText($title);
		if(!$pageTitle->exists()) {
		  return 'Create the page [[MediaWikiBootstrap:TitleBar]]';
		} else {
		  $article = new Article($pageTitle);
		  return $article->getPage()->getContent()->getNativeData();
		}
	}


/*************************************************************************************************/
	function logoBox() {
?>
		<a class="brand pull-left" href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>">
			<img src="<?php $this->text('logopath') ?>" alt="<?php $this->msg('mainpage') ?>" />
		</a>
<?php
	}


/*************************************************************************************************/
	function Connexion() {
?>
	    <ul style="padding:0;margin-left:-5px;">
			<li class="btn btn-light">
				<?php echo Linker::linkKnown(
					SpecialPage::getTitleFor( 'Userlogin' ),
					wfMessage( 'login' )
				) ?>
			</li>
		</ul>
<?php
}
/*************************************************************************************************/
	function navbar()
	{
		$titleBar = $this->getPageRawText('MediaWikiBootstrap:TitleBar');

		$nav = array();
		foreach(explode("\n", $titleBar) as $line) {
			if(trim($line) == '') continue;

			if(preg_match('/^\*\s*\[([^ ]+) +(.+)\]/', $line, $match)) {			
			$nav[] = array('title'=>$match[2], 'link'=>$match[1], 'html'=>true);
			}
			elseif(preg_match('/^\*\s*\[\[(.+)\|(.+)\]\]/', $line, $match)) {
				$nav[] = array('title'=>$match[2], 'link'=>$match[1], 'html'=>false);
			}
			elseif(preg_match('/^\*\s*\[\[(.+)\]\]/', $line, $match)) {
				$nav[] = array('title'=>$match[1], 'link'=>$match[1], 'html'=>false);
				}
			elseif(preg_match('/^\*\*\s*\[\[(.+)\|(.+)\]\]/', $line, $match)) {
				$nav[count($nav)-1]['sublinks'][] = array('title'=>$match[2], 'link'=>$match[1], 'html'=>false);
			}
			elseif(preg_match('/\*\*\s*\[\[(.+)\]\]/', $line, $match)) {
				$nav[count($nav)-1]['sublinks'][] = array('title'=>$match[1], 'link'=>$match[1], 'html'=>false);
			}
			elseif(preg_match('/^\*\s*(.+)/', $line, $match)) {
				$nav[] = array('title'=>$match[1], 'html'=>false);
			}
		}
	    foreach($nav as $topItem) {
			if ($topItem['html']==true)
				$pageTitle = $topItem['title'];
			// else
				// $pageTitle = Title::newFromText($topItem['link']);
			if(array_key_exists('sublinks', $topItem)) {
			 $active = "";
			 foreach($topItem['sublinks'] as $subLink) {
				if ($this->getSkin()->getTitle() == $subLink['link'])
					$active = "current active";
			 }			
			  echo '<li class="dropdown nav-item '.  $active . '" >';
				echo '<a href="#" class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . $topItem['title'] .'</a>';
				echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
				foreach($topItem['sublinks'] as $subLink) {
				  $pageTitle = Title::newFromText($subLink['link']);
				  echo '<li><a class="dropdown-item" href="' . $pageTitle->getLocalURL() .  '">' . $subLink['title']   . '</a>';
				}
				echo '</ul>';
			  echo '</li>';
			} else {
			  if ($topItem['html']==false)
				  $URL = $pageTitle->getLocalURL();
			  else
				  $URL = $topItem['link'];
			  echo '<li class="dropdown nav-item" ' . ($this->data['title'] == $topItem['title'] ? ' class="active"' : '') . '><a class="nav-link" href="' . $URL  . '">' . $topItem['title'] . '</a></li>';
			}
		  }
	}	



/*************************************************************************************************/
	function personal_urls($wgUser)
	{
		if ( count( $this->data['personal_urls'] ) > 0 ) {
		?>
			<li class="dropdown nav-item" data-dropdown="dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo $wgUser->getName(); ?></a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
			<?php foreach($this->data['personal_urls'] as $item): ?>
				<li <?php if (array_key_exists('attributes', $item)) { echo $item['attributes'];} ?>>
					<a class="dropdown-item" href="<?php echo htmlspecialchars($item['href']) ?>"<?php if (array_key_exists('key', $item)) { echo $item['key']; }?><?php if(!empty($item['class'])): ?> class="<?php echo htmlspecialchars($item['class']) ?>"<?php endif; ?>><?php echo htmlspecialchars($item['text']) ?>
					</a>
				</li>
			<?php endforeach; ?>
			</ul>
			</li>
		<?php
		}
	}

/*************************************************************************************************/
	function content_actions()
	{
		if ( count( $this->data['content_actions']) > 0 ) {
	      ?>
			<li class="dropdown nav-item" data-dropdown="dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Page</a>
				<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
					<?php $array = array_keys($this->data['content_actions']); $lastkey = end($array); ?>
					<?php foreach($this->data['content_actions'] as $key => $action) { ?>
					   <li id="ca-<?php echo Sanitizer::escapeIdForAttribute($key) ?>" <?php
						   if($action['class']) { ?>class="<?php echo htmlspecialchars($action['class']) ?>"<?php } ?>
					   ><a class="dropdown-item" href="<?php echo htmlspecialchars($action['href']) ?>"><?php
						   echo htmlspecialchars($action['text']) ?></a> <?php
						   if($key != $lastkey) //echo "&#8226;" ?></li>
					<?php } ?>
				</ul>
			</li>	
		<?php
		}
	}



	/*************************************************************************************************/
	function toolbox() {
		?>
		<li class="dropdown nav-item" data-dropdown="dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Outils</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<?php
				if($this->data['notspecialpage']) { ?>
					<li id="t-whatlinkshere">
						<a class="dropdown-item" href="<?php	echo htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href'])				?>">
							<?php $this->msg('whatlinkshere') ?>
						</a>
					</li>
				<?php
				if( $this->data['nav_urls']['recentchangeslinked'] ) { ?>
					<li id="t-recentchangeslinked">
						<a class="nav-link" href="<?php	echo htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href'])?>"  >
							<?php $this->msg('recentchangeslinked') ?>
						</a>
					</li>
				<?php 	}
				}
				if(isset($this->data['nav_urls']['trackbacklink'])) { ?>
					<li id="t-trackbacklink"><a class="nav-link" href="<?php
						echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
				?>"   ><?php $this->msg('trackbacklink') ?></a></li>
				<?php 	}
				if($this->data['feeds']) { ?>
					<li id="feedlinks"><?php foreach($this->data['feeds'] as $key => $feed) {
					?><span id="feed-<?php echo Sanitizer::escapeIdForAttribute($key) ?>">
					<a class="nav-link" href="<?php
					echo htmlspecialchars($feed['href']) ?>"   ><?php echo htmlspecialchars($feed['text'])?></a>&nbsp;</span>
					<?php } ?></li>
					<?php
				}
				foreach( array('contributions', 'log', 'blockip', 'emailuser', 'upload', 'specialpages') as $special ) {
					if($this->data['nav_urls'][$special]) {
					?>	<li id="t-<?php echo $special ?>">
							<a class="nav-link" href="<?php echo htmlspecialchars($this->data['nav_urls'][$special]['href'])	?>"   >
							<?php $this->msg($special) ?>
							</a>
						</li>
		<?php		}
		}

		if(!empty($this->data['nav_urls']['print']['href'])) { ?>
				<li id="t-print"><a class="nav-link" href="<?php echo htmlspecialchars($this->data['nav_urls']['print']['href'])
				?>"   ><?php $this->msg('printableversion') ?></a></li><?php
		}

		if(!empty($this->data['nav_urls']['permalink']['href'])) { ?>
				<li id="t-permalink"><a class="nav-link" href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href'])
				?>"   ><?php $this->msg('permalink') ?></a></li><?php
		} elseif ($this->data['nav_urls']['permalink']['href'] === '') { ?>
				<li id="t-ispermalink"<?php echo $this->skin->tooltip('t-ispermalink') ?>><?php $this->msg('permalink') ?></li><?php
		}
		?>
		</ul>
	</li>
	<!-- end of personal tools  -->

<?php
	}

	function search_box()
	{
	?>
		<form class="pull-right" style="display:inline;" action="<?php $this->text( 'wgScript' ) ?>" id="search-form">
			<input type="text" placeholder="Search" name="search" onchange="$('#search-form').submit()" />
		</form>
	<?php
	}

	/*************************************************************************************************/
	function footerBox() {
	?>
		<hr>
		<p style="float: left;">
		<?php
		if ($this->data['copyrightico'])
			echo $this->html('copyrightico'); 
		if ($this->data['copyrightico'])
			echo $this->html('copyright'); 
		?>
		</p>
		<p style="float: right;">
		<?php
		$footerlinks = array(
						'numberofwatchingusers', 'credits',
						'about'// 'privacy',  'tagline',
					);
		$lastkey = -1;
		foreach( $footerlinks as $aLink ) {
			if( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
				$lastkey = $aLink;
			}
		}
		foreach( $footerlinks as $aLink ) {
			if( isset( $this->data[$aLink] ) && $this->data[$aLink] ) {
				$this->html($aLink);
			if ($aLink != $lastkey ) echo " | ";
			else echo " ";
			}
		}
		//echo ' | <a href="plan_du_site">' . "Plan" . '</a>';
		echo ' | <a href="Sp&#233;cial:Contact">' . "Contact" . '</a> ';
		if($this->data['poweredbyico']) {
			 $this->html('poweredbyico');
		}
		?>
		</p>
		<?php
	}

	function contentBox()
	{
		if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>

		<div id="bodyContent" class="gumax-bodyContent">
			<h1 id="firstHeading" class="firstHeading gumax-firstHeading" ><?php $this->html('title') ?></h1>
			<div id="contentSub"><?php $this->html('subtitle') ?></div>
			<?php if($this->data['undelete']) { ?><div id="contentSub2"><?php     $this->html('undelete') ?></div><?php } ?>
			<?php if($this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html('newtalk')  ?></div><?php } ?>
			<?php if($this->data['showjumplinks']) { ?><div id="jump-to-nav"><?php $this->msg('jumpto') ?> <a href="#column-one"><?php $this->msg('jumptonavigation') ?></a>, <a href="#searchInput"><?php $this->msg('jumptosearch') ?></a></div><?php } ?>
			<!-- start content -->
			<?php $this->html('bodytext') ?>
			<?php if($this->data['catlinks']) { "<hr>" . $this->html('catlinks'); } ?>
			<!-- end content -->
			<?php if($this->data['dataAfterContent']) { $this->html ('dataAfterContent'); } ?>
			<div class="visualClear"></div>
		</div>
		<?php
	}


}
?>

