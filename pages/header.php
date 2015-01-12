<?php
/*
HLstatsX Community Edition - Real-time player and clan rankings and statistics
Copyleft (L) 2008-20XX Nicholas Hastings (nshastings@gmail.com)
http://www.hlxcommunity.com

HLstatsX Community Edition is a continuation of 
ELstatsNEO - Real-time player and clan rankings and statistics
Copyleft (L) 2008-20XX Malte Bayer (steam@neo-soft.org)
http://ovrsized.neo-soft.org/

ELstatsNEO is an very improved & enhanced - so called Ultra-Humongus Edition of HLstatsX
HLstatsX - Real-time player and clan rankings and statistics for Half-Life 2
http://www.hlstatsx.com/
Copyright (C) 2005-2007 Tobias Oetzel (Tobi@hlstatsx.com)

HLstatsX is an enhanced version of HLstats made by Simon Garner
HLstats - Real-time player and clan rankings and statistics for Half-Life
http://sourceforge.net/projects/hlstats/
Copyright (C) 2001  Simon Garner
            
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

For support and installation notes visit http://www.hlxcommunity.com
*/
	
	if ( !defined('IN_HLSTATS') )
	{
		die('Do not access this file directly.');
	}
	
	/*
	 * HLstatsX Page Header This file will be inserted at the top of every page
	 * generated by HLstats. This file can contain PHP code.
	 */
	 
	// hit counter
	$db->query("UPDATE hlstats_Options SET value=value+1 WHERE keyname='counter_hits';"); 
  
	// visit counter
	if ($_COOKIE['ELstatsNEO_Visit'] == 0) {
		// kein cookie gefunden, also visitcounter erh�hen und cookie setzen
		$db->query("UPDATE hlstats_Options SET value=value+1 WHERE keyname='counter_visits';");
		@setcookie('ELstatsNEO_Visit', '1', time() + ($g_options['counter_visit_timeout'] * 60), '/');   
	}
     
	global $game, $mode;

	$iconpath = IMAGE_PATH . "/icons";
	if (file_exists($iconpath . "/" . $style)) {
			$iconpath = $iconpath . "/" . $style;
	}
	
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
	<link rel="stylesheet" type="text/css" href="css/hlstats.css" />
    <link rel="stylesheet" type="text/css" href="css/ts3.css" />
	<link rel="stylesheet" type="text/css" href="styles/sourcebans.css" />
	<link rel="stylesheet" type="text/css" href="css/SqueezeBox.css" />
<?php if ($mode == 'players'): ?>
	<link rel="stylesheet" type="text/css" href="css/Autocompleter.css" />
<?php endif; ?>
	<link rel="shortcut icon" href="favicon.ico" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>/js/html5shiv.js"></script>
    <![endif]-->
	<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>/js/mootools.js"></script>
	<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>/js/SqueezeBox.js"></script>
	<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>/js/heatmap.js"></script>
<?php if ($g_options['playerinfo_tabs'] == '1'): ?>
	<script type="text/javascript" src="<?php echo INCLUDE_PATH; ?>/js/tabs.js"></script>
<?php endif; ?>

	<title>
<?php
	echo $g_options['sitename']; 
	foreach ($title as $t)
	{
		echo " - $t";
	}
?>
	</title>
</head>
<body> 
<?php
	//JS Check

	if ( $_POST['js'] )
	{
		$_SESSION['nojs'] = 0;
	} else { 
	?>
		<?php if ((!isset($_SESSION['nojs'])) or ($_SESSION['nojs'] == 1)):
		// Send javascript form - if they have javascript enabled it will POST the JS variable, and the code above will update their session variable
		?>
			<!-- Either this is your first visit in a while, or you don\'t have javascript enabled -->
			<form name="jsform" id="jsform" action="" method="post" style="display:none">
				<div>
					<input name="js" type="text" value="true" />
					<script type="text/javascript">
						document.jsform.submit();
					</script>
				</div>
			</form>
			
		<?php
			$_SESSION['nojs'] = 1;
			$g_options['playerinfo_tabs'] = 0;
			$g_options['show_google_map'] = 0;
		?>
		<?php endif; 
	}
		?>
	<?php // Determine if we should show SourceBans links/Forum links ?>
    
	<?php if ($g_options['sourcebans_address'] && file_exists($iconpath . "/title-sourcebans.png")): ?>
		<li class="sourcebans">
        	<a href="<?php echo $g_options['sourcebans_address'] ?>" target="_blank">
            	<img src="<?php echo $iconpath ?>/title-sourcebans.png" alt="SourceBans" />
            </a>
        </li>
	<?php endif; ?>
    
	<?php if ($g_options['forum_address'] && file_exists($iconpath . "/title-forum.png")): ?>
		<li class="forum">
        	<a href="<?php echo $g_options['forum_address'] ?>" target="_blank">
            	<img src="<?php echo $iconpath ?>/title-forum.png" alt="Forum" />
            </a>
        </li>
	<?php endif; ?>

<div class="block">
	
	<div class="headerblock">
		<div class="title">
			<a href="<?php echo $g_options['scripturl']; ?>">
				<img src="<?php echo $iconpath; ?>/title.png" alt="HLstatsX Community Edition" title="HLstatsX Community Edition" />
			</a>
		</div>

<?php

		// Grab count of active games -- if 1, we won't show the games list icons
		$resultGames = $db->query("
			SELECT
				COUNT(code)
			FROM
				hlstats_Games
			WHERE
				hidden='0'
		");
		
		list($num_games) = $db->fetch_row($resultGames);
		
		if ($num_games > 1 && $g_options['display_gamelist'] == 1) :
?>
		<div class="header_gameslist"><?php @include(PAGE_PATH .'/gameslist.php'); ?></div>
		<?php endif; ?>
		<div class="headertabs">
			<ul>
				<li><a href="<?php echo $g_options['scripturl'] ?>"><img src="<?php echo $iconpath; ?>/title-contents.png" alt="Contents" /></a></li>
				<li><a href="<?php echo $g_options['scripturl'] ?>?mode=search"><img src="<?php echo $iconpath; ?>/title-search.png" alt="Search" /></a></li>
				<?php if ($extratabs) { print $extratabs; } ?>				
				<li><a href="<?php echo $g_options['scripturl'] ?>?mode=help"><img src="<?php echo $iconpath; ?>/title-help.png" alt="Help" /></a></li>
			</ul>

		</div>
	</div>
	<div class="location" style="clear:both;width:100%;">
		<ul class="fNormal" style="float:left">
		<?php if ($g_options['sitename'] && $g_options['siteurl']): ?>
			<li>
            	<a href="<?php echo preg_replace('/http:\/\//', '', $g_options['siteurl']) ?>"><?php echo $g_options['sitename'] ?></a>
		<?php endif; ?>
				<span class="arrow">&raquo;</span>
            </li>
            
			<li>
            	<a href="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ?>">HLstatsX</a>
            </li>

		<?php
			$i=0;
			foreach ($location as $l=>$url) 
			{
			
				$url = preg_replace('/%s/', $g_options['scripturl'], $url);
				$url = preg_replace('/&/', '&amp;', $url);
		?>
				<li>
                	<span class="arrow">&raquo;</span>
                </li>
                
                <li>
				<?php if ($url): ?>
					<a href="<?php echo $url ?>"><?php echo $l ?></a>
				<?php else: ?>
					<strong><?php echo $l ?></strong>
				<?php 
					endif;
					$i++; 
				?>
				</li>
            <?php
			}
		?>
		</ul>

	</div>
	<div class="location_under" style="clear:both;width:100%;"></div>
</div>

<br />
      
<div class="content" style="clear:both;">
<?php
	global $mode;
	if ($g_options['bannerdisplay'] != 0 && ($mode == 'contents' || $g_options['bannerdisplay']==1)):
?>    
	<div class="block" style="text-align:center;">
		<img src="<?php echo ((strncmp($g_options['bannerfile'], 'http:/', 6) == 0)?$g_options['bannerfile']:IMAGE_PATH.'/'.$g_options['bannerfile']); ?>" alt="Banner" />
	</div>
<?php endif; ?>        

<?php if ($game != ''): ?>    
    <span class="fHeading">&nbsp;<img src="<?php echo IMAGE_PATH; ?>/downarrow.gif" alt="" />&nbsp;Sections</span><p />
		<ul class="navbar">
			<li class="servers">
            	<a href="<?php echo $g_options['scripturl']  . "?game=$game";  ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-servers.png" alt="Servers" /></a> <a href="<?php echo $g_options['scripturl']  . "?game=$game";  ?>" class="fHeading">Servers
                </a>
            </li>

<?php if ($g_options['nav_globalchat']==1): ?>
			<li class="gamechat">
            	<a href="<?php echo $g_options['scripturl']  . "?mode=chat&amp;game=$game";  ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-chat.png" alt="Chat" />
                </a> 
                <a href="<?php echo $g_options['scripturl']  . "?mode=chat&amp;game=$game";  ?>" class="fHeading">Chat</a>
            </li>
<?php endif; ?>

			<li class="players">
            	<a href="<?php echo $g_options['scripturl'] . "?mode=players&amp;game=$game"; ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-players.png" alt="Players" /></a> 
                    
            	<a href="<?php echo $g_options['scripturl'] . "?mode=players&amp;game=$game"; ?>" class="fHeading">Players</a>
            </li>
            
			<li class="clans">
            	<a href="<?php echo $g_options['scripturl'] . "?mode=clans&amp;game=$game"; ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-clans.png" alt="Clans" />
                </a> 
                <a href="<?php echo $g_options['scripturl'] . "?mode=clans&amp;game=$game"; ?>" class="fHeading">Clans</a>
           </li>

<?php if ($g_options["countrydata"]==1): ?>
			<li class="countries">
            	<a href="<?php echo $g_options['scripturl']  . "?mode=countryclans&amp;game=$game";  ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-countryclans.png" alt="CountryClans" />
               	</a> 
                
                <a href="<?php echo $g_options['scripturl']  . "?mode=countryclans&amp;game=$game&amp;sort=nummembers";  ?>" class="fHeading">Countries</a>
            </li>
<?php endif; ?>

			<li class="awards">
            	<a href="<?php echo $g_options['scripturl'] . "?mode=awards&amp;game=$game"; ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-awards.png" alt="Awards" />
                </a> 
                <a href="<?php echo $g_options['scripturl'] . "?mode=awards&amp;game=$game"; ?>" class="fHeading">Awards</a>
           	</li>
<?php
	// look for actions
	$db->query("SELECT game FROM hlstats_Actions WHERE game='".$game."' LIMIT 1");
	if ($db->num_rows()>0):
?> 
			<li class="actions">
            	<a href="<?php echo $g_options['scripturl'] . "?mode=actions&amp;game=$game"; ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-actions.png" alt="Actions" />
               	</a> 
                <a href="<?php echo $g_options['scripturl'] . "?mode=actions&amp;game=$game"; ?>" class="fHeading">Actions</a>
            </li>
<?php endif; ?>

			<li class="weapons">
            	<a href="<?php echo $g_options['scripturl'] . "?mode=weapons&amp;game=$game"; ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-weapons.png" alt="Weapons" />
                </a> 
                <a href="<?php echo $g_options['scripturl'] . "?mode=weapons&amp;game=$game"; ?>" class="fHeading">Weapons</a>
            </li>
            
			<li class="maps">
            	<a href="<?php echo $g_options['scripturl'] . "?mode=maps&amp;game=$game"; ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-maps.png" alt="Maps" />
                </a> 
                <a href="<?php echo $g_options['scripturl'] . "?mode=maps&amp;game=$game"; ?>" class="fHeading">Maps</a>
            </li>
<?php
	$result = $db->query("SELECT game from hlstats_Roles WHERE game='$game' AND hidden = '0'");
	$numitems = $db->num_rows($result);
	if ($numitems > 0):
?>
			<li class="roles">
            	<a href="<?php echo $g_options['scripturl'] . "?mode=roles&amp;game=$game"; ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-roles.png" alt="Roles" />
                </a> 
                <a href="<?php echo $g_options['scripturl'] . "?mode=roles&amp;game=$game"; ?>" class="fHeading">Roles</a>
           	</li>
<?php endif; ?>

<?php if ($g_options['nav_cheaters'] == 1): ?>
			<li class="bans">
            	<a href="<?php echo $g_options['scripturl'] . "?mode=bans&amp;game=$game"; ?>" class="fHeading">
                	<img src="<?php echo $iconpath; ?>/nav-bans.png" alt="Banned" />
                </a> 
                <a href="<?php echo $g_options['scripturl'] . "?mode=bans&amp;game=$game"; ?>" class="fHeading">Bans</a></li>
<?php endif; ?>
	</ul>
<?php endif; ?>
