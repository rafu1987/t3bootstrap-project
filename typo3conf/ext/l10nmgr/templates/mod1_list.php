<?php
	if ( !is_array($this->getRegistryData()) ) {
		exit('no data');
	}

//var_dump($this->getRegistrydata());
global $BACK_PATH, $LANG;
?>


<?php $gD1 = $this->getDocument(); echo $gD1->startPage($LANG->getLL('general.title')); ?>
<?php $gD2 = $this->getDocument(); echo $gD2->header($LANG->getLL('general.title')); ?>
<?php $gD3 = $this->getDocument(); echo $gD3->section('', nl2br($LANG->getLL('general.description.message'))); ?>
<?php $gD4 = $this->getDocument(); echo $gD4->section($LANG->getLL('general.list.configuration.title'),''); ?>

<?php $gD5 = $this->getDocument(); echo $gD5->spacer(5); ?>

<table id="translationObjectList" class="scrollable" border="1">
	<thead>
		<tr class="bgColor5 tableheader">
			<th><?php echo $LANG->getLL('general.list.headline.info.title'); ?></th>
			<th><?php echo $LANG->getLL('general.list.headline.title.title'); ?></th>
			<th><?php echo $LANG->getLL('general.list.headline.path.title'); ?></th>
			<th><?php echo $LANG->getLL('general.list.headline.depth.title'); ?></th>
			<th><?php echo $LANG->getLL('general.list.headline.tables.title'); ?></th>
			<th><?php echo $LANG->getLL('general.list.headline.exclude.title'); ?></th>
			<th><?php echo $LANG->getLL('general.list.headline.include.title'); ?></th>
			<th><?php echo $LANG->getLL('general.list.headline.incfcewithdefaultlanguage.title'); ?></th>
		</tr>
	</thead>

	  <tbody>
		<?php $pagePermissionClause = $GLOBALS['BE_USER']->getPagePermsClause(1); ?>
		<?php $allConfigurationElementsStruct = $this->getRegistryData(); ?>
		<?php for( reset($allConfigurationElementsStruct); list(,$configurationElementArray) = each($allConfigurationElementsStruct); ) { ?>

			<?php if (!is_array(t3lib_BEfunc::readPageAccess($configurationElementArray['pid'],$pagePermissionClause))) {
				continue;
			} ?>

		<tr class="bgColor3">
			<td align="center">
				<a class="tooltip" href="#<?php echo 'tooltip_' . $configurationElementArray['uid']; ?>">
					<?php $gD6 = $this->getDocument(); $gD6->icons(1); ?>
				</a>

				<?php $parentPageArray = t3lib_BEfunc::getRecord('pages',$configurationElementArray['pid']); ?>
				<?php $staticInfoTablesArray = t3lib_BEfunc::getRecord('static_languages',t3lib_div::intval_positive($configurationElementArray['sourceLangStaticId'])); ?>

				<div style="display:none;" id="<?php echo 'tooltip_' . $configurationElementArray['uid'] ;?>" class="infotip">
					<table class="infodetail" cellspacing="0" cellpadding="0">
						<tr>
							<td>mmm<?php echo $LANG->getLL('general.list.infodetail.pid.title'); ?></td>
							<td><?php echo $parentPageArray['title']; echo ' (' . $parentPageArray['uid'] . ')'?></td>
						</tr>
						<tr>
							<td><?php echo $LANG->getLL('general.list.infodetail.title.title'); ?></td>
							<td><?php echo $configurationElementArray['title']; ?></td>
						</tr>
						<tr>
							<td><?php echo $LANG->getLL('general.list.infodetail.filenameprefix.title'); ?></td>
							<td><?php echo $configurationElementArray['filenameprefix']; ?></td>
						</tr>
						<tr>
							<td><?php echo $LANG->getLL('general.list.infodetail.depth.title'); ?></td>
							<td><?php echo $configurationElementArray['depth']; ?></td>
						</tr>
						<tr>
							<td><?php echo $LANG->getLL('general.list.infodetail.sourceLangStaticId.title'); ?></td>
							<td><?php echo $staticInfoTablesArray['lg_name_en']; ?></td>
						</tr>
						<tr>
							<td><?php echo $LANG->getLL('general.list.infodetail.tablelist.title'); ?></td>
							<td><?php echo $configurationElementArray['tablelist']; ?></td>
						</tr>
						<tr>
							<td><?php echo $LANG->getLL('general.list.infodetail.exclude.title'); ?></td>
							<td><?php echo $configurationElementArray['exclude']; ?></td>
						</tr>
						<tr>
							<td><?php echo $LANG->getLL('general.list.infodetail.include.title'); ?></td>
							<td><?php echo $configurationElementArray['include']; ?></td>
						</tr>
						<tr>
							<td><?php echo $LANG->getLL('general.list.infodetail.displaymode.title'); ?></td>
							<td><?php echo $configurationElementArray['displaymode']; ?></td>
						</tr>
						<tr>
							<td><?php echo $LANG->getLL('general.list.infodetail.incfcewithdefaultlanguage.title'); ?></td>
							<td><?php echo $configurationElementArray['incfcewithdefaultlanguage']; ?></td>
						</tr>
					</table>
				</div>
			</td>
			<td><?php echo '<a href="' . t3lib_div::resolveBackPath($BACK_PATH .t3lib_extMgm::extRelPath('l10nmgr')) . 'cm1/index.php?id=' . $configurationElementArray['uid'] . '&srcPID=' .  t3lib_div::intval_positive($this->getPageId()) . '">' . $configurationElementArray['title']  . '</a>'; ?></td>
			<td><?php echo current(t3lib_BEfunc::getRecordPath($configurationElementArray['pid'], '1', 20, 50)); ?></td>
			<td><?php echo $configurationElementArray['depth']; ?></td>
			<td><?php echo $configurationElementArray['tablelist']; ?></td>
			<td><?php echo $configurationElementArray['exclude']; ?></td>
			<td><?php echo $configurationElementArray['include']; ?></td>
			<td><?php echo $configurationElementArray['incfcewithdefaultlanguage']; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php $gD8 = $this->getDocument(); echo $gD8->spacer(10); ?>
<?php $gD9 = $this->getDocument(); echo $gD9->endPage(); ?>
