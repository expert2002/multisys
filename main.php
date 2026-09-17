<?php 
	include('../eng/init.php'); 
	include('../eng/eng_functions.php');
	include('../eng/eng_security.php'); 
	include('../eng/eng_make_from_database.php');
	include('../eng/eng_make_menu.php'); 
	$_SESSION['system']['HTTP_REFERER']=$_SESSION['system']['PHP_SELF'];
	$_SESSION['system']['PHP_SELF']='main.php';
	$_SESSION['system']['PHP_FOLDER']='personal';
	$_SESSION['system']['PHP_MENU']='personalmain';
	$make_from_database=new make_from_database('1'); 
	$make_menu=new make_menu($_SESSION['system']['PHP_SELF'],$_SESSION['system']['PHP_FOLDER'],$_SESSION['system']['PHP_MENU'],'bash_main.php');
	if((isset($_SESSION['dostup']['personal'])) && ($_SESSION['dostup']['personal'] >= 1))	
	{
	unset($_SESSION['personal']['personal_id'], $_SESSION['personal']['personal_id_nkpd'], $_SESSION['personal']['personal_firm'], $_SESSION['personal']['personal_address'], $_SESSION['personal']['personal_name'], $_SESSION['personal']['personal_egn'], $_SESSION['personal']['personal_obshtina'], $_SESSION['personal']['personal_oblast'], $_SESSION['personal']['personal_city'], $_SESSION['personal']['personal_dlujnost'], $_SESSION['personal']['personal_postypil'], $_SESSION['personal']['personal_pol']);

	$where=' ';	//tursene po egn
	if((!empty($_POST['egn'])) && (strlen($_POST['egn']) > 3) && (ctype_digit($_POST['egn'])) && ($_SESSION['system']['HTTP_REFERER'] == 'main.php'))
	{
		$where .= " AND p.egn like '%".$_POST['egn']."%'";
	}	//tursene po ime
	if((!empty($_POST['name_check'])) && (strlen($_POST['name_check']) > 3) && (clean_text($_POST['name_check'])) && ($_SESSION['system']['HTTP_REFERER'] == 'main.php'))
	{
		$where .= " AND ((p.fname like '%".$_POST['name_check']."%') OR (p.mname like '%".$_POST['name_check']."%') OR (p.lname like '%".$_POST['name_check']."%'))";	
	}	

	$q="SELECT pf.id_firm, 
				 pf.id as id,
				 pf.arch,
				 pf.id_nkpd,
				 pf.id_person,
				 p.id as pid,
				 p.egn as egn,
				 CONCAT_WS(' ', p.fname, p.mname, p.lname) as name,	
				 nkpd.kod_".$_SESSION['kod_nkpd']." as kodnkpd,  nkpd.dlujnost
			FROM personal_firm pf 
			LEFT JOIN db_nkpd nkpd ON nkpd.id=pf.id_nkpd 
			LEFT JOIN personal p ON p.id=pf.id_person
			WHERE pf.id_firm=".intval($_SESSION['firm']['firm_id'])."
			".$where."  
			AND pf.napusnal=0 
			AND pf.arch='0' 
			AND pf.id_contract_type=1
			ORDER BY p.egn ASC";
	$data_osnovni=query_rows($q); unset($q, $where);
	$q="SELECT pf.id_firm, 
				 pf.id as id,
				 pf.arch,
				 pf.id_nkpd,
				 pf.id_person,
				 p.id as pid,
				 p.egn as egn,
				 CONCAT_WS(' ', p.fname, p.mname, p.lname) as name,	
				 nkpd.kod_".$_SESSION['kod_nkpd']." as kodnkpd,  nkpd.dlujnost
			FROM personal_firm pf 
			LEFT JOIN db_nkpd nkpd ON nkpd.id=pf.id_nkpd 
			LEFT JOIN personal p ON p.id=pf.id_person
			WHERE pf.id_firm=".intval($_SESSION['firm']['firm_id'])."
			AND pf.napusnal=0 
			AND pf.arch='0' 
			AND pf.id_contract_type=2
			ORDER BY p.egn ASC";
	$data_vtori=query_rows($q); unset($q);
	$q="SELECT pf.id_firm, 
				 pf.id as id,
				 pf.arch,
				 pf.id_nkpd,
				 pf.id_person,
				 p.id as pid,
				 p.egn as egn,
				 CONCAT_WS(' ', p.fname, p.mname, p.lname) as name,	
				 nkpd.kod_".$_SESSION['kod_nkpd']." as kodnkpd,  nkpd.dlujnost
			FROM personal_firm pf 
			LEFT JOIN db_nkpd nkpd ON nkpd.id=pf.id_nkpd 
			LEFT JOIN personal p ON p.id=pf.id_person
			WHERE pf.id_firm=".intval($_SESSION['firm']['firm_id'])."
			AND pf.napusnal=0 
			AND pf.arch='0' 
			AND pf.id_contract_type=0
			ORDER BY p.egn ASC";
	$data_samo=query_rows($q); unset($q);
?>
<?php include('../include/incl_metatags.php');?>
<script language="javascript">	
	function validate_test()
	{	
		var fields=new Array('name_check', 'dotcomma', 'Въведете само букви в името на лекаря!', 
							   'egn', 'digits', 'ЕГН трябва да бъде съставено само от цифри');
							   
		if(validate(fields) == true)
		{

			return true;
		}
		else 
		{
			return false;
		}
	}
</script>
</head>
<body <?php include('../eng/eng_info_screen.php');?>>
<?php $make_menu->child_menu(); ?>
<div id="message">
	<?php $make_menu->root_menu(); unset($make_menu);?>
	<?php include('../include/incl_header.php');?>
	<div id="page">
		<div id="law">
		<p align="left"><h3>Данни за работещите в предприятието</h3></p>
			<table width="100%" border="0" id="add_personal">
				<td width="80%" align="left"><h3>Работещи на основен трудов договор</h3></td>
				<td width="20%" align="right"><form id="normi" name="normi" action="ae_personal.php" method="post">
													<input type="image" src="../images/insert.gif" alt="добави персонал" />
												</form></td>
			</table>
			<?php if(!empty($data_osnovni))
			{
			?>
			<form id="personal" name="personal" method="post" onSubmit="return validate_test();">
			<table width="100%" align="center" id="search_personal">
				<tr>
					<td width="15%" align="right">ЕГН :</td>
					<td width="20%"><input type="text" name="egn" id="egn"/></td>
					<td width="10%"><input type="submit" value="търси"/></td>
					<td width="10%"></td>
					<td width="15%" align="right">име :</td>
					<td width="20%"><input type="text" name="name_check" id="name_check"/></td>
					<td width="10%"><input type="submit" value="търси"/></td>
				</tr>
			</table>
			</form>					
			<table cellpadding="0" cellspacing="0" border="0" id="osnovni" class="sortable">
				<thead>
					<tr>
						<th><h3>ЕГН</h3></th>
						<th><h3>име презиме фамилия</h3></th>
						<th><h3>длъжност</h3></th>
						<th class="nosort"><h3>избери лице</h3></th>
						<th class="nosort"><h3>отпечатай</h3></th>
						<th class="nosort"><h3>промени</h3></th>
					</tr>
				</thead>
				<tbody>
				<?php if(!empty($data_osnovni)) foreach($data_osnovni as $k=>$v)
				{
				?>
					<tr>
						<td><?php nbsp($v['egn']);?></td>							
						<td><?php nbsp($v['name']);?></td>
						<td><?php nbsp($v['kodnkpd'].' '.$v['dlujnost']);?></td>
						<td align="center">
							<form id="personal" name="personal" action="personal.php" method="post">
								<input type="hidden" value="<?php echo $v['pid'];?>" name="pid" id="pid" />
								<input type="hidden" value="<?php echo $v['id'];?>" name="id" id="id" />
								<input type="image" src="../images/user.gif" alt="избери лице" />
							</form>
						</td>
						<td align="center">
							<?php if((isset($_SESSION['dostup']['personal'])) && ($_SESSION['dostup']['personal'] >= 2)) { ?>
							<form id="print" name="print" action="../spravki/spravki.php" method="post">
								<input type="hidden" value="<?php echo $v['pid'];?>" name="pid" id="pid" />
								<input type="hidden" value="<?php echo $v['id'];?>" name="id" id="id" />
								<input type="hidden" value="view_personal" name="view" id="view" />
								<input type="image" src="../images/print.gif" alt="отпечатай" title="Отпечатай справка за лицето" />
							</form>
							<?php } else { ?>
							<img src="../images/noread.gif" alt="Нямате достъп до тези опции" title="Нямате достъп до тези опции" />
							<?php } ?>
						</td>
						<td align="center">
							<?php if((isset($_SESSION['dostup']['personal'])) && ($_SESSION['dostup']['personal'] >= 3)) { ?>
							<form id="personal" name="personal" action="ae_personal.php" method="post">
								<input type="hidden" value="<?php echo $v['pid'];?>" name="pid" id="pid" />
								<input type="hidden" value="<?php echo $v['id'];?>" name="id" id="id" />
								<input type="hidden" name="action" id="action" value="edit_personal"/>
								<input type="image" src="../images/edit.gif" alt="промени" title="Промени данните на лицето" />
							</form>
							<?php } else { ?>
							<img src="../images/noread.gif" alt="Нямате достъп до тези опции" title="Нямате достъп до тези опции" />
							<?php } ?>
						</td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
			<div id="controls">
				<div id="perpage">
					<select onChange="sorter1.size(this.value)">
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="20">20</option>
						<option value="50">50</option>
						<option value="100" selected="selected">100</option>
						<option value="200">200</option>
						<option value="500">500</option>
					</select>
					<span>реда на страница</span>
				</div>
				<div id="navigation">
					<img src="../images/first.gif" width="16" height="16" alt="начална страница" onClick="sorter1.move(-1,true)" />
					<img src="../images/previous.gif" width="16" height="16" alt="предишна страница" onClick="sorter1.move(-1)" />
					<img src="../images/next.gif" width="16" height="16" alt="следваща страница" onClick="sorter1.move(1)" />
					<img src="../images/last.gif" width="16" height="16" alt="последна страница" onClick="sorter1.move(1,true)" />
				</div>
				<div id="text">Страница <span id="currentpage1"></span> от <span id="pagelimit1"></span></div>
			</div>
			<script type="text/javascript">
			var sorter1=new TINY.table.sorter("sorter1");
			sorter1.head="head";
			sorter1.asc="asc";
			sorter1.desc="desc";
			sorter1.even="evenrow";
			sorter1.odd="oddrow";
			sorter1.evensel="evenselected";
			sorter1.oddsel="oddselected";
			sorter1.paginate=true;
			sorter1.currentid="currentpage1";
			sorter1.limitid="pagelimit1";
			sorter1.init("osnovni",0);
			</script>
			<?php
			}
			?>
			<table width="100%" border="0" id="add_personal">
				<td width="80%" align="left"><h3>Работещи на втори трудов договор при същият работодател</h3></td>
				<td width="20%" align="right"><form id="normi" name="normi" action="ae_personal.php" method="post">
													<input type="image" src="../images/insert.gif" alt="добави персонал" />
												</form></td>
			</table>
			<?php if(!empty($data_vtori))
			{
			?>
			<table cellpadding="0" cellspacing="0" border="0" id="vtori" class="sortable">
				<thead>
					<tr>
						<th><h3>ЕГН</h3></th>
						<th><h3>име презиме фамилия</h3></th>
						<th><h3>длъжност</h3></th>
						<th class="nosort"><h3>избери лице</h3></th>
						<th class="nosort"><h3>отпечатай</h3></th>
						<th class="nosort"><h3>промени</h3></th>
					</tr>
				</thead>
				<tbody>
				<?php if(!empty($data_vtori)) foreach($data_vtori as $k=>$v)
				{
				?>
					<tr>
						<td><?php nbsp($v['egn']);?></td>							
						<td><?php nbsp($v['name']);?></td>
						<td><?php nbsp($v['kodnkpd'].' '.$v['dlujnost']);?></td>
						<td align="center">
							<form id="personal" name="personal" action="personal.php" method="post">
								<input type="hidden" value="<?php echo $v['pid'];?>" name="pid" id="pid" />
								<input type="hidden" value="<?php echo $v['id'];?>" name="id" id="id" />
								<input type="image" src="../images/user.gif" alt="избери лице" />
							</form>
						</td>
						<td align="center">
							<?php if((isset($_SESSION['dostup']['personal'])) && ($_SESSION['dostup']['personal'] >= 2)) { ?>
							<form id="print" name="print" action="../spravki/spravki.php" method="post">
								<input type="hidden" value="<?php echo $v['pid'];?>" name="pid" id="pid" />
								<input type="hidden" value="<?php echo $v['id'];?>" name="id" id="id" />
								<input type="hidden" value="view_personal" name="view" id="view" />
								<input type="image" src="../images/print.gif" alt="отпечатай" title="Отпечатай справка за лицето" />
							</form>
							<?php } else { ?>
							<img src="../images/noread.gif" alt="Нямате достъп до тези опции" title="Нямате достъп до тези опции" />
							<?php } ?>
						</td>
						<td align="center">
							<?php if((isset($_SESSION['dostup']['personal'])) && ($_SESSION['dostup']['personal'] >= 3)) { ?>
							<form id="personal" name="personal" action="ae_personal.php" method="post">
								<input type="hidden" value="<?php echo $v['pid'];?>" name="pid" id="pid" />
								<input type="hidden" value="<?php echo $v['id'];?>" name="id" id="id" />
								<input type="hidden" name="action" id="action" value="edit_personal"/>
								<input type="image" src="../images/edit.gif" alt="промени" title="Промени данните на лицето" />
							</form>
							<?php } else { ?>
							<img src="../images/noread.gif" alt="Нямате достъп до тези опции" title="Нямате достъп до тези опции" />
							<?php } ?>
						</td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
			<div id="controls">
				<div id="perpage">
					<select onChange="sorter2.size(this.value)">
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="20">20</option>
						<option value="50">50</option>
						<option value="100" selected="selected">100</option>
						<option value="200">200</option>
						<option value="500">500</option>
					</select>
					<span>реда на страница</span>
				</div>
				<div id="navigation">
					<img src="../images/first.gif" width="16" height="16" alt="начална страница" onClick="sorter2.move(-1,true)" />
					<img src="../images/previous.gif" width="16" height="16" alt="предишна страница" onClick="sorter2.move(-1)" />
					<img src="../images/next.gif" width="16" height="16" alt="следваща страница" onClick="sorter2.move(1)" />
					<img src="../images/last.gif" width="16" height="16" alt="последна страница" onClick="sorter2.move(1,true)" />
				</div>
				<div id="text">Страница <span id="currentpage2"></span> от <span id="pagelimit2"></span></div>
			</div>
			<script type="text/javascript">
			var sorter2=new TINY.table.sorter("sorter2");
			sorter2.head="head";
			sorter2.asc="asc";
			sorter2.desc="desc";
			sorter2.even="evenrow";
			sorter2.odd="oddrow";
			sorter2.evensel="evenselected";
			sorter2.oddsel="oddselected";
			sorter2.paginate=true;
			sorter2.currentid="currentpage2";
			sorter2.limitid="pagelimit2";
			sorter2.init("vtori",0);
			</script>
			<?php
			}
			?>
			<table width="100%" border="0" id="add_personal">
				<td width="80%" align="left"><h3>Самоосигуряващи се и собственици</h3></td>
				<td width="20%" align="right"><form id="normi" name="normi" action="ae_personal.php" method="post">
													<input type="image" src="../images/insert.gif" alt="добави персонал" />
												</form></td>
			</table>
			<?php if(!empty($data_samo))
			{
			?>
			<table cellpadding="0" cellspacing="0" border="0" id="samo" class="sortable">
				<thead>
					<tr>
						<th><h3>ЕГН</h3></th>
						<th><h3>име презиме фамилия</h3></th>
						<th><h3>длъжност</h3></th>
						<th class="nosort"><h3>избери лице</h3></th>
						<th class="nosort"><h3>отпечатай</h3></th>
						<th class="nosort"><h3>промени</h3></th>
					</tr>
				</thead>
				<tbody>
				<?php if(!empty($data_samo)) foreach($data_samo as $k=>$v)
				{
				?>
					<tr>
						<td><?php nbsp($v['egn']);?></td>							
						<td><?php nbsp($v['name']);?></td>
						<td><?php nbsp($v['kodnkpd'].' '.$v['dlujnost']);?></td>
						<td align="center">
							<form id="personal" name="personal" action="personal.php" method="post">
								<input type="hidden" value="<?php echo $v['pid'];?>" name="pid" id="pid" />
								<input type="hidden" value="<?php echo $v['id'];?>" name="id" id="id" />
								<input type="image" src="../images/user.gif" alt="избери лице" />
							</form>
						</td>
						<td align="center">
							<?php if((isset($_SESSION['dostup']['personal'])) && ($_SESSION['dostup']['personal'] >= 2)) { ?>
							<form id="print" name="print" action="../spravki/spravki.php" method="post">
								<input type="hidden" value="<?php echo $v['pid'];?>" name="pid" id="pid" />
								<input type="hidden" value="<?php echo $v['id'];?>" name="id" id="id" />
								<input type="hidden" value="view_personal" name="view" id="view" />
								<input type="image" src="../images/print.gif" alt="отпечатай" title="Отпечатай справка за лицето" />
							</form>
							<?php } else { ?>
							<img src="../images/noread.gif" alt="Нямате достъп до тези опции" title="Нямате достъп до тези опции" />
							<?php } ?>
						</td>
						<td align="center">
							<?php if((isset($_SESSION['dostup']['personal'])) && ($_SESSION['dostup']['personal'] >= 3)) { ?>
							<form id="personal" name="personal" action="ae_personal.php" method="post">
								<input type="hidden" value="<?php echo $v['pid'];?>" name="pid" id="pid" />
								<input type="hidden" value="<?php echo $v['id'];?>" name="id" id="id" />
								<input type="hidden" name="action" id="action" value="edit_personal"/>
								<input type="image" src="../images/edit.gif" alt="промени" title="Промени данните на лицето" />
							</form>
							<?php } else { ?>
							<img src="../images/noread.gif" alt="Нямате достъп до тези опции" title="Нямате достъп до тези опции" />
							<?php } ?>
						</td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
			<div id="controls">
				<div id="perpage">
					<select onChange="sorter3.size(this.value)">
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="20">20</option>
						<option value="50">50</option>
						<option value="100" selected="selected">100</option>
						<option value="200">200</option>
						<option value="500">500</option>
					</select>
					<span>реда на страница</span>
				</div>
				<div id="navigation">
					<img src="../images/first.gif" width="16" height="16" alt="начална страница" onClick="sorter3.move(-1,true)" />
					<img src="../images/previous.gif" width="16" height="16" alt="предишна страница" onClick="sorter3.move(-1)" />
					<img src="../images/next.gif" width="16" height="16" alt="следваща страница" onClick="sorter3.move(1)" />
					<img src="../images/last.gif" width="16" height="16" alt="последна страница" onClick="sorter3.move(1,true)" />
				</div>
				<div id="text">Страница <span id="currentpage3"></span> от <span id="pagelimit3"></span></div>
			</div>
			<script type="text/javascript">
			var sorter3=new TINY.table.sorter("sorter3");
			sorter3.head="head";
			sorter3.asc="asc";
			sorter3.desc="desc";
			sorter3.even="evenrow";
			sorter3.odd="oddrow";
			sorter3.evensel="evenselected";
			sorter3.oddsel="oddselected";
			sorter3.paginate=true;
			sorter3.currentid="currentpage3";
			sorter3.limitid="pagelimit3";
			sorter3.init("samo",0);
			</script>
			<?php
			}
			?>
		</div>
		<div style="clear: both;">&nbsp;</div>
	</div>
	<div id="footer">
		<?php include('../include/incl_copyright.php');?>
	</div>
</div>
<script type="text/javascript">
disableSelection(document.body) 
</script>
</body>
</html>
<?php 
	}
?>
