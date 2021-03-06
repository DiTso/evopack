<?php
	if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE !== true){ die();}
	if (!class_exists('TransAlias')) {
		require_once MODX_BASE_PATH.'assets/plugins/transalias/transalias.class.php';
	}
	$trans = new TransAlias($modx);
	$trans->loadTable('russian', 'Yes');
    function getDirContents($dir, &$results = array()){
		$files = scandir($dir);
		foreach($files as $key => $value){
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			if(!is_dir($path)) {
				$results[] = $path;
				} else if($value != "." && $value != "..") {
				getDirContents($path, $results);
				$results[] = $path;
			}
		}
		return array_reverse($results);
	}
	if ($_POST['generate'])
	{
		$categories = array();
		$res = $modx->db->query('Select * from '.$modx->getFullTableName('categories'));
		while($row = $modx->db->getRow($res)) $categories[$row['id']] = $row['category'];
		if ($_POST['name']) $name = $_POST['name'];
		else $name = 'untitled_puck_'.time();
		$folder = __DIR__.'/'.$name.'/';
		if(!is_dir($folder)) mkdir($folder);
		if ((isset($_POST['chunks'])) && (count($_POST['chunks'])))
		{
			if (!is_dir($folder.'install/')) mkdir($folder.'install/');
			if (!is_dir($folder.'install/assets/')) mkdir($folder.'install/assets/');
			if (!is_dir($folder.'install/assets/chunks/')) mkdir($folder.'install/assets/chunks/');
			foreach($_POST['chunks'] as $idc)
			{	
				$res = $modx->db->query('Select * from '.$modx->getFullTableName('site_htmlsnippets').' where id='.$idc);	
				$chunk = $modx->db->getRow($res);				
				$fp = fopen($folder.'install/assets/chunks/'.$trans->stripAlias($chunk['name'],'lowercase alphanumeric','dash').'.tpl', "w");		
				if (!$chunk['description']) $chunk['description']=$chunk['name'];
				$text = '/**'.PHP_EOL;
				$text.=' * '.$chunk['name'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * '.$chunk['description'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * @category	chunk'.PHP_EOL;				
				$text.=' * @internal	@modx_category '.$categories[$chunk['category']].PHP_EOL;
				$text.=' * @internal	@installset base'.PHP_EOL;
				$text.=' * @internal	@overwrite true'.PHP_EOL;
				$text.=' */'.PHP_EOL;
				$text.=$chunk['snippet'].PHP_EOL;				
				fwrite($fp, $text);			
				fclose($fp);
			}			
		}	
		if ((isset($_POST['snippets'])) && (count($_POST['snippets'])))
		{
			if (!is_dir($folder.'install/')) mkdir($folder.'install/');
			if (!is_dir($folder.'install/assets/')) mkdir($folder.'install/assets/');
			if (!is_dir($folder.'install/assets/snippets/')) mkdir($folder.'install/assets/snippets/');
			foreach($_POST['snippets'] as $idc)
			{	
				$res = $modx->db->query('Select * from '.$modx->getFullTableName('site_snippets').' where id='.$idc);	
				$snippet = $modx->db->getRow($res);				
				$fp = fopen($folder.'install/assets/snippets/'.$trans->stripAlias($snippet['name'],'lowercase alphanumeric','dash').'.tpl', "w");			
				if (!$snippet['description']) $snippet['description']=$snippet['name'];
				$text = '//<?php'.PHP_EOL;
				$text.= '/**'.PHP_EOL;
				$text.=' * '.$snippet['name'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * '.$snippet['description'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * @category	snippet'.PHP_EOL;				
				$text.=' * @internal	@modx_category '.$categories[$snippet['category']].PHP_EOL;
				$text.=' * @internal	@installset base'.PHP_EOL;
				$text.=' * @internal	@overwrite true'.PHP_EOL;
				$text.=' * @internal	@properties '.$snippet['properties'].PHP_EOL;
				$text.=' */'.PHP_EOL;
				$text.=$snippet['snippet'].PHP_EOL;				
				fwrite($fp, $text);			
				fclose($fp);
			}			
		}		
		if ((isset($_POST['tvs'])) && (count($_POST['tvs'])))
		{
			if (!is_dir($folder.'install/')) mkdir($folder.'install/');
			if (!is_dir($folder.'install/assets/')) mkdir($folder.'install/assets/');
			if (!is_dir($folder.'install/assets/tvs/')) mkdir($folder.'install/assets/tvs/');
			foreach($_POST['tvs'] as $idc)
			{	
				$res = $modx->db->query('Select * from '.$modx->getFullTableName('site_tmplvars').' where id='.$idc);	
				$tv = $modx->db->getRow($res);				
				$fp = fopen($folder.'install/assets/tvs/'.$trans->stripAlias($tv['name'],'lowercase alphanumeric','dash').'.tpl', "w");	
				if (!$tv['description']) $tv['description']=$tv['name'];
				$text = '/**'.PHP_EOL;
				$text.=' * '.$tv['name'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * '.$tv['description'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * @category	tv'.PHP_EOL;				
				$text.=' * @name	'.$tv['name'].PHP_EOL;
				$text.=' * @internal	@caption '.$tv['caption'].PHP_EOL;
				$text.=' * @internal	@input_type '.$tv['type'].PHP_EOL;
				$text.=' * @internal	@modx_category '.$categories[$snippet['category']].PHP_EOL;
				$text.=' * @internal	@input_default '.$tv['default_text'].PHP_EOL;
				$text.=' * @internal	@input_options '.$tv['elements'].PHP_EOL;				
				$text.=' */'.PHP_EOL;							
				fwrite($fp, $text);			
				fclose($fp);
			}			
		}
		if ((isset($_POST['templates'])) && (count($_POST['templates'])))
		{
			if (!is_dir($folder.'install/')) mkdir($folder.'install/');
			if (!is_dir($folder.'install/assets/')) mkdir($folder.'install/assets/');
			if (!is_dir($folder.'install/assets/templates/')) mkdir($folder.'install/assets/templates/');
			foreach($_POST['templates'] as $idc)
			{									
				$res = $modx->db->query('Select * from '.$modx->getFullTableName('site_templates').' where id='.$idc);	
				$templates = $modx->db->getRow($res);				
				if(!$templates['description']) $templates['description']=$templates['templatename'];
				$fp = fopen($folder.'install/assets/templates/'.$trans->stripAlias($templates['templatename'],'lowercase alphanumeric','dash').'.tpl', "w");			
				$text = '/**'.PHP_EOL;
				$text.=' * '.$templates['templatename'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * '.$templates['description'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * @category	template'.PHP_EOL;				
				$text.=' * @internal	@modx_category '.$categories[$templates['category']].PHP_EOL;
				$text.=' * @internal	@installset base'.PHP_EOL;
				$text.=' * @internal	@overwrite true'.PHP_EOL;
				$text.=' * @internal	@save_sql_id_as '.$trans->stripAlias($templates['templatename'],'lowercase alphanumeric','dash').'_SQL_ID'.PHP_EOL;
				$text.=' */'.PHP_EOL;
				$text.=$templates['content'].PHP_EOL;				
				fwrite($fp, $text);			
				fclose($fp);
			}			
		}
		if ((isset($_POST['modules'])) && (count($_POST['modules'])))
		{
			if (!is_dir($folder.'install/')) mkdir($folder.'install/');
			if (!is_dir($folder.'install/assets/')) mkdir($folder.'install/assets/');
			if (!is_dir($folder.'install/assets/modules/')) mkdir($folder.'install/assets/modules/');
			foreach($_POST['modules'] as $idc)
			{	
				$res = $modx->db->query('Select * from '.$modx->getFullTableName('site_modules').' where id='.$idc);	
				$module = $modx->db->getRow($res);				
				$fp = fopen($folder.'install/assets/modules/'.$trans->stripAlias($module['name'],'lowercase alphanumeric','dash').'.tpl', "w");			
				if (!$module['description']) $module['description']=$module['name'];
				$text = '/**'.PHP_EOL;
				$text.=' * '.$module['name'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * '.$module['description'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * @category	module'.PHP_EOL;				
				$text.=' * @internal	@modx_category '.$categories[$module['category']].PHP_EOL;
				$text.=' * @internal	@installset base'.PHP_EOL;
				$text.=' * @internal	@properties '.$module['properties'].PHP_EOL;
				$text.=' * @internal	@guid '.$module['guid'].PHP_EOL;
				$text.=' * @internal	@shareparams 1'.PHP_EOL;
				$text.=' * @internal	@overwrite true'.PHP_EOL;
				$text.=' */'.PHP_EOL;
				$text.=$module['modulecode'].PHP_EOL;				
				fwrite($fp, $text);			
				fclose($fp);
			}			
		}
		if ((isset($_POST['plugins'])) && (count($_POST['plugins'])))
		{
			if (!is_dir($folder.'install/')) mkdir($folder.'install/');
			if (!is_dir($folder.'install/assets/')) mkdir($folder.'install/assets/');
			if (!is_dir($folder.'install/assets/plugins/')) mkdir($folder.'install/assets/plugins/');
			foreach($_POST['plugins'] as $idc)
			{	
				$res = $modx->db->query('Select * from '.$modx->getFullTableName('site_plugins').' where id='.$idc);	
				$plugin = $modx->db->getRow($res);				
				$fp = fopen($folder.'install/assets/plugins/'.$trans->stripAlias($plugin['name'],'lowercase alphanumeric','dash').'.tpl', "w");			
				if (!$plugin['description']) $plugin['description']=$plugin['name'];
				$text='//<?php'.PHP_EOL;
				$text.='/**'.PHP_EOL;
				$text.=' * '.$plugin['name'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * '.$plugin['description'].PHP_EOL;
				$text.=' *'.PHP_EOL;
				$text.=' * @category    plugin'.PHP_EOL;				
				$text.=' * @internal    @events '.$modx->db->getValue('SELECT GROUP_CONCAT(`name`) FROM '.$modx->getFullTableName('site_plugin_events').' left join '.$modx->getFullTableName('system_eventnames').' ON '.$modx->getFullTableName('system_eventnames').'.`id`= '.$modx->getFullTableName('site_plugin_events').'.`evtid` WHERE `pluginid`='.$idc).PHP_EOL;
				$text.=' * @internal    @modx_category '.$categories[$plugin['category']].PHP_EOL;
				$text.=' * @internal    @properties '.$plugin['properties'].PHP_EOL;
				$text.=' * @internal    @disabled '.$plugin['disabled'].PHP_EOL;
				$text.=' * @internal    @installset base'.PHP_EOL;								
				$text.=' */'.PHP_EOL;
				$text.=$plugin['plugincode'].PHP_EOL;				
				fwrite($fp, $text);			
				fclose($fp);
			}			
		}
		$zip = new ZipArchive();		
		$zip_name = __DIR__.'/pucks/'.$name.'.zip'; 			
		if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
		{				
			exit("* Sorry ZIP creation failed at this time");
		}
		else
		{
			if ((isset($_POST['files'])) && (count($_POST['files'])))
			{
				$zips = array();
				foreach($_POST['files'] as $s) 
				{
					if (!is_dir($s)) $zips[] = $s;
					else
					{
						$zips[] = $s;
						$t = getDirContents($s);
						foreach($t as $a) $zips[] = $a;
					}
				}
				foreach($zips as $f)
				{
					$f2 = str_replace(MODX_BASE_PATH,'',$f);
					if (is_dir($f)) 
					{	
						$zip->addEmptyDir('/'.$name.'/'.$f2);
					}
					else $zip->addFile($f, '/'.$name.'/'.$f2);
				}
			}	
			foreach(getDirContents($folder) as $sources)
			{
				$f1 = str_replace(__DIR__,'',$sources);
				if (is_dir($sources)) 
				{
					$zip->addEmptyDir($f1);
				}
				else $zip->addFile($sources, $f1);
			}
		}	
		$zip->close();		
		exec("rm -R ".$folder);
	}
	if (isset($_POST['path']))
	{	
		$assets = $_POST['path'];		
		$path = scandir($assets);
		foreach($path as $as)
		{
			if (is_dir($assets.$as.'/'))
			{
				if (($as!='.') && ($as!='..')) $disr[] = $as;
			}
			else $files[] = $as;
		}
		foreach ($disr as $as) echo '<p style="margin-bottom:0;"><input type="checkbox" name="files[]" value="'.$assets.$as.'/" class="form-check-input files""> <a href="javascript:void(0);" class="view_folder" data-path="'.$assets.$as.'/"><i class="fa fa-folder-o FilesFolder"></i> '.$as.'</a></p>';
		foreach ($files as $as) echo '<p  style="margin-bottom:0;"><input type="checkbox" name="files[]" class="form-check-input files" value="'.$assets.$as.'"> <i class="fa fa-file-o FilesPage"></i> '.$as.'</p>';
		exit();				
	}
	$heading_panel = '<div class="panel-heading">
	<span class="panel-title">
	<a class="accordion-toggle" id="togglesite_templates0" href="#collapsesite_templates0" data-cattype="site_templates" data-catid="0" title="">
	<span class="category_name">
	<strong>[+name+]</strong>
	</span>
	</a>
	</span>
	</div>';
	$tplRow = '
	<li style="padding-left:15px !important;">
	<div class="rTable">
	<div class="rTableRow">
	<div class="mainCell elements_description">
	<label class="form-check form-check-label">
	<input type="checkbox" name="[+ch_name+][]" class="form-check-input [+ch_name+]" value="[+id+]">	
	<a class="man_el_name" href="index.php?a=[+a+]&amp;id=[+id+]" target="_blank" style="padding-left:0;">
	<b class="text-primary">[+name+]</b>
	<small>([+id+])</small> <span class="elements_descr">[+description+]</span>
	</a>
	</label>
	</div>            
	</div>
	</div>
	</li>';
?>
<html>
	<head>
		<title>Модуль создания установочных пакетов</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<script src="media/script/jquery/jquery.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="media/script/tabpane.js"></script>		
		<meta name="viewport" content="initial-scale=1.0,user-scalable=no,maximum-scale=1,width=device-width" />
		<meta http-equiv="Content-Type" content="text/html; charset=<?=$modx->config['modx_charset'];?>" />
		<link rel="stylesheet" type="text/css" href="<?=$modx->config['site_manager_url'];?>media/style/default/css/styles.min.css" />
		<style>
			.text-primary,td{font-size: 0.8125rem !important; cursor:ponter;}
		</style>
	</head>
	<body class="sectionBody">
		<h1><i class="fa fa-th"></i>Создание установочных пакетов</h1>
		<form method="post" action="">
			<div class="tab-pane " id="docManagerPane">
				<script type="text/javascript">
					tpResources = new WebFXTabPane(document.getElementById('docManagerPane'));
				</script>
				<div class="tab-page" id="tabGeneral">
					<h2 class="tab"><i class="fa fa-home"></i> Основное</h2>
					<script type="text/javascript">tpResources.addTabPage(document.getElementById('tabGeneral'));</script>
					<div class="tab-body">
						<h3>Создание пакета</h3>
						<p id="el">Шаблоны - <b id="templates">0</b> ТВ-параметры - <b id="tvs">0</b> Чанки - <b id="chunks">0</b> Сниппеты - <b id="snippets">0</b> Плагины - <b id="plugins">0</b> Файлы - <b id="files">0</b></p>
						<input type="hidden" name="generate" value="1">
						<div style="position:relative;">
							<input type="text" name="name" placeholder="Введите название пакета">
							<input type="submit" value="Сформировать пакет" style="position: absolute;right: 0;top: 0; z-index:22;">
						</div>
						<hr>
						<h3>Сформированные пакеты</h3>
						<?php
							$fol = scandir(__DIR__.'/pucks/');
							if (count($fol)==2) echo 'Сформированных пакетов нет.';
							else
							{
								foreach($fol as $as)
								{
									if (($as!='.') && ($as!='..') && ($as!='index.html'))
									{
										echo '<p><a href="./../assets/modules/evopuck/pucks/'.$as.'">'.$as.'</a></p>';
									}
								}
							}
						?>
					</div>
				</div>
				<div class="tab-page" id="tabTemplates">
					<h2 class="tab"><i class="fa fa-newspaper-o"></i> Шаблоны</h2>
					<script type="text/javascript">tpResources.addTabPage(document.getElementById('tabTemplates'));</script>
					<div class="panel-group">
						<div class=" resourceTable">
							<p style="margin:1.25rem 0 0 1.25rem;">Пока только содержимое...(</p>
							<?php
								$res = $modx->db->query('SELECT '.$modx->getFullTableName('categories').'.`category`,'.$modx->getFullTableName('site_templates').'.`category` as cat FROM '.$modx->getFullTableName('site_templates').' 
								left join '.$modx->getFullTableName('categories').'
								on '.$modx->getFullTableName('categories').'.`id` = '.$modx->getFullTableName('site_templates').'.`category`
								group by '.$modx->getFullTableName('site_templates').'.`category`');
								while ($row = $modx->db->getRow($res))
								{
									if (!$row['category']) $name = 'Без категории';
									else $name = $row['category'];
									echo str_replace('[+name+]',$name,$heading_panel);
									echo '<div id="collapsesite_htmlsnippets2" class="panel-collapse collapse in" aria-expanded="true">';
									echo '<ul class="elements" style="column-count: 1; margin-top: 5px;">';
									$res2 = $modx->db->query('Select * from '.$modx->getFullTableName('site_templates').' where category='.$row['cat']);
									while ($row2 = $modx->db->getRow($res2))
									{
										$fields = array('[+id+]','[+a+]','[+name+]','[+description+]','[+ch_name+]');
										$values = array($row2['id'],'16',$row2['templatename'],$row2['description'],'templates');
										echo str_replace($fields,$values,$tplRow);
									}
									echo '</ul>';
									echo '</div>';
								}
							?>
						</div>					
					</div>
				</div>
				<div class="tab-page" id="tabTemplateVariables">
					<h2 class="tab"><i class="fa fa-list-alt"></i> TV-параметры</h2>
					<script type="text/javascript">tpResources.addTabPage(document.getElementById('tabTemplateVariables'));</script>
					<div class="panel-group">
						<div class=" resourceTable">
							<?php
								$res = $modx->db->query('SELECT '.$modx->getFullTableName('categories').'.`category`,'.$modx->getFullTableName('site_tmplvars').'.`category` as cat FROM '.$modx->getFullTableName('site_tmplvars').' 
								left join '.$modx->getFullTableName('categories').'
								on '.$modx->getFullTableName('categories').'.`id` = '.$modx->getFullTableName('site_tmplvars').'.`category`
								group by '.$modx->getFullTableName('site_tmplvars').'.`category`');
								while ($row = $modx->db->getRow($res))
								{
									if (!$row['category']) $name = 'Без категории';
									else $name = $row['category'];
									echo str_replace('[+name+]',$name,$heading_panel);
									echo '<div id="collapsesite_htmlsnippets2" class="panel-collapse collapse in" aria-expanded="true">';
									echo '<ul class="elements" style="column-count: 1; margin-top: 5px;">';
									$res2 = $modx->db->query('Select * from '.$modx->getFullTableName('site_tmplvars').' where category='.$row['cat']);
									while ($row2 = $modx->db->getRow($res2))
									{
										$fields = array('[+id+]','[+a+]','[+name+]','[+description+]','[+ch_name+]');
										$values = array($row2['id'],'301',$row2['name'],$row2['description'],'tvs');
										echo str_replace($fields,$values,$tplRow);
									}
									echo '</ul>';
									echo '</div>';
								}
							?>
						</div>					
					</div>
				</div>			
				<div class="tab-page" id="tabChunks">
					<h2 class="tab"><i class="fa fa-th-large"></i> Чанки</h2>
					<script type="text/javascript">tpResources.addTabPage(document.getElementById('tabChunks'));</script>
					<div class="panel-group">
						<div class=" resourceTable">
							<?php
								$res = $modx->db->query('SELECT '.$modx->getFullTableName('categories').'.`category`,'.$modx->getFullTableName('site_htmlsnippets').'.`category` as cat FROM '.$modx->getFullTableName('site_htmlsnippets').' 
								left join '.$modx->getFullTableName('categories').'
								on '.$modx->getFullTableName('categories').'.`id` = '.$modx->getFullTableName('site_htmlsnippets').'.`category`
								group by '.$modx->getFullTableName('site_htmlsnippets').'.`category`');
								while ($row = $modx->db->getRow($res))
								{
									if (!$row['category']) $name = 'Без категории';
									else $name = $row['category'];
									echo str_replace('[+name+]',$name,$heading_panel);
									echo '<div id="collapsesite_htmlsnippets2" class="panel-collapse collapse in" aria-expanded="true">';
									echo '<ul class="elements" style="column-count: 1; margin-top: 5px;">';
									$res2 = $modx->db->query('Select * from '.$modx->getFullTableName('site_htmlsnippets').' where category='.$row['cat']);
									while ($row2 = $modx->db->getRow($res2))
									{
										$fields = array('[+id+]','[+a+]','[+name+]','[+description+]','[+ch_name+]');
										$values = array($row2['id'],'7',$row2['name'],$row2['description'],'chunks');
										echo str_replace($fields,$values,$tplRow);
									}
									echo '</ul>';
									echo '</div>';
								}
							?>
						</div>					
					</div>
				</div>	
				<div class="tab-page" id="tabSnippets">
					<h2 class="tab"><i class="fa fa-code"></i> Сниппеты</h2>
					<script type="text/javascript">tpResources.addTabPage(document.getElementById('tabSnippets'));</script>
					<div class="panel-group">
						<div class=" resourceTable">
							<?php
								$res = $modx->db->query('SELECT '.$modx->getFullTableName('categories').'.`category`,'.$modx->getFullTableName('site_snippets').'.`category` as cat FROM '.$modx->getFullTableName('site_snippets').' 
								left join '.$modx->getFullTableName('categories').'
								on '.$modx->getFullTableName('categories').'.`id` = '.$modx->getFullTableName('site_snippets').'.`category`
								group by '.$modx->getFullTableName('site_snippets').'.`category`');
								while ($row = $modx->db->getRow($res))
								{
									if (!$row['category']) $name = 'Без категории';
									else $name = $row['category'];
									echo str_replace('[+name+]',$name,$heading_panel);
									echo '<div id="collapsesite_htmlsnippets2" class="panel-collapse collapse in" aria-expanded="true">';
									echo '<ul class="elements" style="column-count: 1; margin-top: 5px;">';
									$res2 = $modx->db->query('Select * from '.$modx->getFullTableName('site_snippets').' where category='.$row['cat']);
									while ($row2 = $modx->db->getRow($res2))
									{
										$fields = array('[+id+]','[+a+]','[+name+]','[+description+]','[+ch_name+]');
										$values = array($row2['id'],'22',$row2['name'],$row2['description'],'snippets');
										echo str_replace($fields,$values,$tplRow);
									}
									echo '</ul>';
									echo '</div>';
								}
							?>
						</div>					
					</div>
				</div>	
				<div class="tab-page" id="tabPlugins">
					<h2 class="tab"><i class="fa fa-plug"></i> Плагины</h2>
					<script type="text/javascript">tpResources.addTabPage(document.getElementById('tabPlugins'));</script>
					<div class="panel-group">
						<div class=" resourceTable">
							<?php
								$res = $modx->db->query('SELECT '.$modx->getFullTableName('categories').'.`category`,'.$modx->getFullTableName('site_plugins').'.`category` as cat FROM '.$modx->getFullTableName('site_plugins').' 
								left join '.$modx->getFullTableName('categories').'
								on '.$modx->getFullTableName('categories').'.`id` = '.$modx->getFullTableName('site_plugins').'.`category`
								group by '.$modx->getFullTableName('site_plugins').'.`category`');
								while ($row = $modx->db->getRow($res))
								{
									if (!$row['category']) $name = 'Без категории';
									else $name = $row['category'];
									echo str_replace('[+name+]',$name,$heading_panel);
									echo '<div id="collapsesite_htmlsnippets2" class="panel-collapse collapse in" aria-expanded="true">';
									echo '<ul class="elements" style="column-count: 1; margin-top: 5px;">';
									$res2 = $modx->db->query('Select * from '.$modx->getFullTableName('site_plugins').' where category='.$row['cat']);
									while ($row2 = $modx->db->getRow($res2))
									{
										$fields = array('[+id+]','[+a+]','[+name+]','[+description+]','[+ch_name+]');
										$values = array($row2['id'],'102',$row2['name'],$row2['description'],'plugins');
										echo str_replace($fields,$values,$tplRow);
									}
									echo '</ul>';
									echo '</div>';
								}
							?>
						</div>					
					</div>
				</div>	
				<div class="tab-page" id="tabModules">
					<h2 class="tab"><i class="fa fa-cubes"></i>  Модули</h2>
					<script type="text/javascript">tpResources.addTabPage(document.getElementById('tabModules'));</script>
					<div class="panel-group">
						<div class=" resourceTable">
							<?php
								$res = $modx->db->query('SELECT '.$modx->getFullTableName('categories').'.`category`,'.$modx->getFullTableName('site_modules').'.`category` as cat FROM '.$modx->getFullTableName('site_modules').' 
								left join '.$modx->getFullTableName('categories').'
								on '.$modx->getFullTableName('categories').'.`id` = '.$modx->getFullTableName('site_modules').'.`category`
								group by '.$modx->getFullTableName('site_modules').'.`category`');
								while ($row = $modx->db->getRow($res))
								{
									if (!$row['category']) $name = 'Без категории';
									else $name = $row['category'];
									echo str_replace('[+name+]',$name,$heading_panel);
									echo '<div id="collapsesite_htmlsnippets2" class="panel-collapse collapse in" aria-expanded="true">';
									echo '<ul class="elements" style="column-count: 1; margin-top: 5px;">';
									$res2 = $modx->db->query('Select * from '.$modx->getFullTableName('site_modules').' where category='.$row['cat']);
									while ($row2 = $modx->db->getRow($res2))
									{
										$fields = array('[+id+]','[+a+]','[+name+]','[+description+]','[+ch_name+]');
										$values = array($row2['id'],'102',$row2['name'],$row2['description'],'modules');
										echo str_replace($fields,$values,$tplRow);
									}
									echo '</ul>';
									echo '</div>';
								}
							?>
						</div>					
					</div>
				</div>	
				<div class="tab-page" id="tabFiles">
					<h2 class="tab"><i class="fa fa-file"></i> Файлы</h2>
					<script type="text/javascript">tpResources.addTabPage(document.getElementById('tabFiles'));</script>
					<div class="tab-body">
						<?php		
							$assets = MODX_BASE_PATH.'assets/';
							$path = scandir($assets);
							foreach($path as $as)
							{
								if (is_dir($assets.$as.'/'))
								{
									if (($as!='.') && ($as!='..')) $disr[] = $as;
								}
								else $files_web[] = $as;
							}
							foreach ($disr as $as) echo '<p style="margin-bottom:0;margin-left: 15px;"><input type="checkbox" name="files[]" value="'.$assets.$as.'/" class="form-check-input files"> <a href="javascript:void(0);" class="view_folder" data-path="'.$assets.$as.'/"><i class="fa fa-folder-o FilesFolder"></i> '.$as.'</a></p>';
							foreach ($files_web as $as) echo '<p  style="margin-bottom:0;margin-left: 15px;"><input type="checkbox" name="files[]" class="form-check-input files"> <i class="fa fa-file-o FilesPage"></i> '.$as.'</p>';
						?>
					</div>
				</div>
			</div>
		</form>
		<style>
			.opened > a{font-weight:700;}
			.sub_catalog{margin-left:25px;}
			#el b{padding-right:30px;}
			.resourceTable ul.elements{margin:0 !important;}
			.resourceTable .panel-title>a{    padding: 5px 2.25rem !important;}
		</style>
		<script>
			$(document).on('change','.form-check-input',function(){
				$('#templates').html($('.templates:checked').length);
				$('#tvs').html($('.tvs:checked').length);
				$('#chunks').html($('.chunks:checked').length);
				$('#snippets').html($('.snippets:checked').length);
				$('#plugins').html($('.plugins:checked').length);
				$('#files').html($('.files:checked').length);
				$('#modules').html($('.modules:checked').length);
			});
			$(document).on('click','.view_folder',function(){
				if ($(this).parent().hasClass('opened'))
				{
					$(this).parent().removeClass('opened').next().remove();
				}
				else
				{
					$(this).parent().addClass('down');
					$(this).parent().addClass('opened');
					$.ajax({
						type: "POST",
						url: location.href,
						data: { path: $(this).data('path')}
					}).done(function(result)
					{
						$('.down').after('<div class="sub_catalog">'+result+'</div>');
						$('.down').removeClass('down');
					});
				}
			});
		</script>
	</body>
</html>		
