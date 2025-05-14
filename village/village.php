<?php
error_reporting(E_ERROR | E_PARSE);

include 'config.php';
$state = $_REQUEST['state'];
$district = isset($_REQUEST['district'])?($_REQUEST['district'].', '):'';
$tehsil = isset($_REQUEST['tehsil'])?($_REQUEST['tehsil'].', '):'';
$village = isset($_REQUEST['village'])?($_REQUEST['village']):'';
$state1 =str_replace('-',' ',$_REQUEST['state']);
$district1 = isset($_REQUEST['district'])?str_replace('-',' ',$_REQUEST['district']):'';
$district_url=str_replace(', ','',$district1);
$tehsil_url=$_REQUEST['tehsil'];

$q_state="select ms.id as state_id,md.id as district_id,mt.tehsil,mt.id as tehsil_id,mgp.gp as village_name from moisearch_gp mgp left join moisearch_tehsil as mt on mgp.tehsil_id=mt.id left join moisearch_district md on mt.district_id=md.id left join moisearch_state ms on md.state_id=ms.id where mgp.slug='".$village."'and mt.tehsil_url='".$tehsil_url."' and md.district='".$district_url."' and ms.state='".$state1."' limit 1";

$result=mysqli_query($db, $q_state);
$tot_rows=mysqli_num_rows($result);
if($tot_rows>0){
$row=mysqli_fetch_assoc($result);
$state_id=$row['state_id'];
$district_id=$row['district_id'];
$tehsil_id=$row['tehsil_id'];
$tehsilname=$row['tehsil'];
$village1=$row['village_name'].', ';


$more =isset($_REQUEST['more'])?($_REQUEST['more']):'';
$select="select off_name,pincode from vf_pincodes where";
$where1=" state='$state1'";
$where2=" and district='$district1'";
$where3=" and taluk='$tehsilname'";
$where4=" and off_name like '$tehsilname"."%'";
$where5=" and off_name like '$village1"."%'";
$where6=" and off_name like '$district1"."%'";
$select1="select distinct(city),std from vf_stdcodes where";
$newWhere1=" state='$state1'";
$newWhere2=" and city='$district1'";
$newWhere3=" and city='$tehsilname'";
$newWhere4=" and city='$village1'";
$flag=1;
$message="";
$message2="";

$country = 'India';

$query_pin=$select.$where1.$where2.$where3.$where5." limit 1";
$query_std=$select1.$newWhere1.$newWhere3.$newWhere4." limit 1";

$result_pin=mysqli_query($db, $query_pin);
$result_std=mysqli_query($db, $query_std);

if(mysqli_num_rows($result_pin)==0){
$query_pin=$select.$where1.$where2.$where3.$where5." limit 1";
$result_pin=mysqli_query($db, $query_pin);
if(mysqli_num_rows($result_pin)==0)
{
$query_pin=$select.$where1.$where2.$where3.$where4." limit 1";
$result_pin=mysqli_query($db, $query_pin);
if(mysqli_num_rows($result_pin)==0)
{ 
$query_pin=$select.$where1.$where2.$where3." limit 1";
$result_pin=mysqli_query($db, $query_pin);
if(mysqli_num_rows($result_pin) > 0){$message="Post offices in tehsil <b>".$tehsilname."</b> are as follows:";}
if(mysqli_num_rows($result_pin)==0)
{
$query_pin=$select.$where1.$where2.$where5." limit 1";
$result_pin=mysqli_query($db, $query_pin);
if(mysqli_num_rows($result_pin)==0)
{
$query_pin=$select.$where1.$where2.$where4." limit 1";
$result_pin=mysqli_query($db, $query_pin);
if($tot_rows>0){
$query_pin=$select.$where1.$where2." limit 1";
$result_pin=mysqli_query($db, $query_pin);
if(mysqli_num_rows($result_pin) > 0){$message="Post offices in district <b>".$district1."</b> are as follows:";}
}
}
}
}
}
}

if(mysqli_num_rows($result_std)==0)
{
$query_std=$select1.$newWhere1.$newWhere3." limit 1";
$result_std=mysqli_query($db, $query_std);
if(mysqli_num_rows($result_std)==0)
{ $query_std=$select1.$newWhere1.$newWhere2." limit 1";
$result_std=mysqli_query($db, $query_std);
if(mysqli_num_rows($result_std)==0)
{ $query_std=$select1.$newWhere1." limit 1";
$result_std=mysqli_query($db, $query_std);
if(mysqli_num_rows($result_std) > 0)
{
$message2="Std codes in state <b>".$state1."</b> are as follows:";
}
}

}

}




$address = ucwords(str_replace('-',' ',$village.','.$district.$state.', '.$country));

$count_pin=1;
$tot_rows_pin=mysqli_num_rows($result_pin);
if($tot_rows_pin > 0){
$pin_data='<tr><th class="thead" colspan="2">Pincdoes </th><tr>';
while($row_pin=mysqli_fetch_array($result_pin)){
$pin_data.='<tr><td>'.$row_pin["off_name"].'</td><td>'.$row_pin["pincode"].'</td></tr>';
}
}


$tot_rows_std=mysqli_num_rows($result_std);
if($tot_rows_std > 0){
$std_data='<tr><th class="thead" colspan="2">STD codes </th><tr>';
while($row_std=mysqli_fetch_array($result_std))
{ 
$std_data.='<tr><td>'.$row_std["city"].'</td><td>'.$row_std["std"].'</td></tr>';
}
$std_data.= '</ul>';
}

$q_villages='select mgp.gp,mgp.slug,mt.tehsil_url, md.district,ms.state from moisearch_gp mgp left join moisearch_tehsil mt on mgp.tehsil_id=mt.id left join moisearch_district md on mt.district_id=md.id left join moisearch_state ms on md.state_id=ms.id where ms.id='.$state_id.' and md.id='.$district_id.' and mt.id='.$tehsil_id.' limit 200';

$result=mysqli_query($db, $q_villages);
if(mysqli_num_rows($result)>0)
{
while($row_v=mysqli_fetch_assoc($result)){
$sname=strtolower(str_replace(' ','-',trim($row_v['state'])));
$dname=strtolower(str_replace(' ','-',trim($row_v['district'])));
$tname=$row_v['tehsil_url'];
$vname=strtolower(str_replace(' ','-',$row_v['slug']));
$village_urls.="<li><a href='http://localhost/villages/".$sname."/".$dname."/".$tname."/".$vname.".html'>".$row_v['gp']."</a></li>";
}
}
$state_url=$state;
$district_url=str_replace(', ','',$district);
$tehsil_url=str_replace(', ','',$tehsil);
	$state = ucwords(strtolower(str_replace('-',' ',$_REQUEST['state'])));
	$district = ucwords(strtolower(str_replace('-',' ',$_REQUEST['district'])));
	$tehsil = ucwords(strtolower(str_replace('-',' ',$_REQUEST['tehsil'])));
	$village = ucwords(strtolower(str_replace('-',' ',$_REQUEST['village'])));
	$title = ''.$village.' Village | Map of '.$village.' Village in '.$tehsil.' Tehsil, '.$district.' of '.$state;
	$h1 = $title;
	$h2='Map of '.$village.' Village in '.$tehsil.', '.$district.' of '.$state;
	$description = ''.$village.' Village | Map of '.$village.' village in '.$tehsil.' Tehsil, '.$district.', '.$state.'.';
	$keywords = 'Map of '.$village.', '.$village.' Village Map, '.$village.' Village in '.$district.', '.$village.' Village in '.$state.', '.$village.' Village location map, '.$village.' Road Map';

$page_url=explode('/',$_SERVER['REQUEST_URI']);
$canonical=$page_url[1].'/'.$page_url[2].'/'.$page_url[3].'/'.$page_url[4].'/'.$page_url[5];
$amp_link=$page_url[1].'/amp/'.$page_url[2].'/'.$page_url[3].'/'.$page_url[4].'/'.$page_url[5];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-BXSFPEHNGD"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-BXSFPEHNGD');
</script>
<title><?php echo $title;?></title>
<meta name="description" content="<?php echo $description;?>" />
<meta name="keywords" content="<?php echo $keywords;?>" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script language="JavaScript" src="https://www.mapsofindia.com/js_2009/style.js" type="text/Javascript"></script>
<script src="https://www.mapsofindia.com/widgets/electionsutility/js/responsive-style.js" type="text/Javascript"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<link rel="stylesheet" href="http://localhost/villages/style.css"/>
<style type="text/css" media="screen">
@import 'https://www.mapsofindia.com/style_2009/style-new.css';
@import 'https://www.mapsofindia.com/style_2009/res-style.css';
</style>
<style>
.table_vill{margin-top:15px;}
.table_vill th{text-align:center}
.thead {text-align:left!important;padding:0 5px}
</style>
<script type='text/javascript'>
var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
(function() {
var gads = document.createElement('script');
gads.async = true;
gads.type = 'text/javascript';
var useSSL = 'https:' == document.location.protocol;
gads.src = (useSSL ? 'https:' : 'http:') + 
'//www.googletagservices.com/tag/js/gpt.js';
var node = document.getElementsByTagName('script')[0];
node.parentNode.insertBefore(gads, node);
})();
</script>

    <script type='text/javascript'>
googletag.cmd.push(function() {
if (window.innerWidth > 770) {
googletag.defineSlot('/5535731/MDPLleaderboard', [[200, 200], [950, 90], [970, 250], [300, 250], [960, 90], [728, 90], [250, 250], [750, 200], [970, 90], [700, 90], [300, 100], [180, 150], [750, 100]], 'div-gpt-ad-1743571624422-0').addService(googletag.pubads());
googletag.defineSlot('/5535731/MDPL_footerad_multisizedX90', [[250, 250], [970, 250], [970, 90], [728, 90]], 'div-gpt-ad-1743571906029-0').addService(googletag.pubads());
googletag.defineSlot('/5535731/MDPL_Footerstickyad_multisizedX90', [[728, 90], [970, 90]], 'div-gpt-ad-1743572033107-0').addService(googletag.pubads());
googletag.defineSlot('/5535731/MDPL_leftgutterspace_multisizedx600', [[120, 240], [120, 600]], 'div-gpt-ad-1743572146447-0').addService(googletag.pubads());
googletag.defineSlot('/5535731/MDPL_rightgutterspace_multisizedx600', [[120, 240], [120, 600]], 'div-gpt-ad-1743572219950-0').addService(googletag.pubads());
}
else if (window.innerWidth < 770){
googletag.defineSlot('/5535731/MDPL_mobile_leaderboard_320X50', [[320, 50], [300, 50]], 'div-gpt-ad-1743584808969-0').addService(googletag.pubads());
googletag.defineSlot('/5535731/MDPL_Mobile_Footer', [[250, 250], [300, 250], [180, 150]], 'div-gpt-ad-1743584600735-0').addService(googletag.pubads());
googletag.defineSlot('/5535731/MDPL_mobile_middlead_multisizedx250', [[300, 250], [250, 250], [300, 50], [180, 150]], 'div-gpt-ad-1743585203217-0').addService(googletag.pubads());
googletag.defineSlot('/5535731/MDPL_Mobile_stickyfooter320x50', [[320, 50], [300, 50]], 'div-gpt-ad-1743584998716-0').addService(googletag.pubads());
}
googletag.pubads().enableSingleRequest();
//googletag.pubads().disableInitialLoad();
googletag.enableServices();
});
</script>
<!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8993220319430392"crossorigin="anonymous"></script> -->
<script data-cfasync="false" type="text/javascript">
(function(w, d) {
    var s = d.createElement('script');
    s.src = '//cdn.adpushup.com/46218/adpushup.js';
    s.crossOrigin='anonymous'; 
    s.type = 'text/javascript'; s.async = true;
    (d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
    w.adpushup = w.adpushup || {que:[]};
})(window, document);

</script>
 <style>
.mbban{display:none;width:100%;text-align:center;}
.desktop-leftG {display: block;}
.desktop-rightG {display: block;}
.desktop-head {display: block;}
.desktop-menu {display: flex;justify-content: center;align-items: center; width: 970px;margin: 0 auto;height: 250px;}
.desktop-footer {display: flex;justify-content: center; align-items: center;  width: 970px;margin: 0 auto;height: 250px;}
.mobile-head {display: none;}
.mobile-menu {display: none;}
.mobile-middle {display: none;}
.mobile-footer-ad {display: none;}
.mobile-footer-sticky {display: none;}
.video-ad{width: 640px;height: 440px;margin: 0 auto;}
@media screen and (max-width:768px){
.mbban{display:block;margin:5px 0;}
.mbban img{width:300px;}
.desktop-leftG {display: none;}
.desktop-rightG {display: none;}
.desktop-head {display: none;}
.desktop-menu {display: none;}
.desktop-footer {display: none;}
.display-footer-sticky {display: none;}
.mobile-head {display: block;}
.mobile-menu {display: flex;justify-content: center; align-items: center; width: 320px;margin: 0 auto;height: 50px;}
.mobile-middle {display: block; width: 300px;margin: 0 auto;height: 250px;}
.mobile-footer-ad {display: flex;justify-content: center; align-items: center; width: 300px;margin: 0 auto;height: 250px;}
.bottom-menu-bg {margin: 10px 0 10px 0 !important;}
.bottom-links {padding-bottom: 0px !important;}
.main.bottom-lnk {margin-bottom: 0px !important;}
.video-ad{width: 320px;height: 275px;margin: 0 auto;}
.mobile-footer-sticky {display: block; text-align: center;}
}
 </style>

</head>
<body>
<!-- /5535731/MDPL_leftgutterspace_multisizedx600 -->
<div class="desktop-leftG">
<div id='div-gpt-ad-1743572146447-0' style='position: fixed;
    z-index: 2000;
    left: 0px;
    margin-left: 5px;
    top: 5px;
    width: 120px;
    height: 600px;
    display: block;
    text-align: center; min-width: 120px; min-height: 240px;'>
  <script>
    googletag.cmd.push(function() { googletag.display('div-gpt-ad-1743572146447-0'); });
  </script>
</div>
</div>
<!-- /5535731/MDPL_rightgutterspace_multisizedx600 -->
<div class="desktop-rightG">
<div id='div-gpt-ad-1743572219950-0' style='right: 0px;
    top: 5px;
    width: 120px;
    height: 600px;
    position: fixed;
    pointer-events: none;
    margin-right: 5px;
    z-index: -9999; min-width: 120px; min-height: 240px;'>
  <script>
    googletag.cmd.push(function() { googletag.display('div-gpt-ad-1743572219950-0'); });
  </script>
</div>
</div>
 
<div  class="main">
<?php virtual ('/moi-header-logo.html'); ?>
</div>
<div  class="main">
<script language="JavaScript" type="text/javascript">
<!--
tab_link();
//-->
</script>
</div>
<div  class="main">
<div  class="navigation"><a href="https://www.mapsofindia.com/">India Map</a> &raquo; <a href="http://localhost/villages/">Villages</a> &raquo; <a href="http://localhost/villages/<?php echo $state_url; ?>/"><?php echo $state; ?></a> &raquo; <a href="http://localhost/villages/<?php echo $state_url.'/'.$district_url; ?>/"><?php echo $district;?></a> &raquo;  <a href="http://localhost/villages/<?php echo $state_url.'/'.$district_url.'/'.$tehsil_url; ?>/"><?php echo $tehsil;?></a> &raquo;  <?php echo $village;?></div>
<div class="google_search">
<table cellpadding="5">
<tr>
<td>
<script language="JavaScript" type="text/javascript">
<!--
google_search();
//-->
</script>
</td>

</tr>
</table>
</div>
</div>
<!-- /5535731/MDPLleaderboard -->
<div class="desktop-menu">
<div id='div-gpt-ad-1743571624422-0' style='min-width: 180px; min-height: 90px;'>
  <script>
    googletag.cmd.push(function() { googletag.display('div-gpt-ad-1743571624422-0'); });
  </script>
</div>
</div>
<!-- /5535731/MDPL_mobile_leaderboard_320X50 -->
<div class="mobile-menu">
<div id='div-gpt-ad-1743584808969-0' style='min-width: 300px; min-height: 50px;'>
  <script>
    googletag.cmd.push(function() { googletag.display('div-gpt-ad-1743584808969-0'); });
  </script>
</div>
</div>
<div  class="main">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
<td valign="top" width="11"><img src="https://www.mapsofindia.com/images2009/top-L-curve-base.gif" width="11" height="10" alt="" /></td>
<td class="grey_bg">&nbsp;</td>
<td valign="top" width="11"><img src="https://www.mapsofindia.com/images2009/top-R-curve-base.gif" width="11" height="10" alt="" /></td>
</tr>
</table>
</div>
<div  class="main">
<div  class="grey_bg1">
<?php include 'left_link.php';?>
<div id="content-main">
<div  class="content-panel">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
<td valign="top" width="10"><img src="https://www.mapsofindia.com/images2009/H1-Lbase.gif" width="10" height="30" alt="" /></td>
<td class="header-base"><h1><?php echo $h1;?></h1></td>
<td valign="top" width="10"><img src="https://www.mapsofindia.com/images2009/H1-R-base.gif" width="10" height="30" alt="" /></td>
</tr>
</table>
</div>
<div  class="content-panel1">


<div  class="content-panel2">
<table border="1" cellpadding="0" cellspacing="0" class="table_vill">
<tr><th colspan='2'><h2>About <?php echo $village.' Village'; ?></h2></th></tr>
<tr><td>Tehsil</td><td><?php echo $tehsil;?></td></tr>
<tr><td>District</td><td><?php echo $district;?></td></tr>
<tr><td>State</td><td><?php echo $state;?></td></tr>

</table>
<br><br>
<div  class="image">
<div  class="text">

<?php echo '<h2>'.$h2.'</h2>';?>

<div class="cont">
<div>
<iframe width="100%" height="350px"id="gmap" frameborder="2px" scrolling="no" marginheight="0" marginwidth="0" 

src="https://maps.google.co.in/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $village ?>+<?php echo $district ?>+<?php echo $state ?>&amp;output=embed&amp;iwloc=near?>">
</iframe>
</div>
<div  class="disclaimer">
<sup>*</sup><?php echo $village; ?> Google map shows the location of <?php echo $village; ?> village under <?php echo $tehsil ?>, <?php echo $district ?> of <?php echo $state; ?> state using Google Maps data.<br /><br />
</div>
</div>
<br />
<div>
<a href="https://www.mapsofindia.com/custom-maps/village-level-maps.html"><img src="https://www.mapsofindia.com/custom-maps/banners/house-hold-banner.png" alt=""></a>
</div>
<div class="video-ad">
<div id="13db4aa1-4df7-4402-8d7f-f1ef0360ea7c" class="_ap_apex_ad" max-height="360">
  <script>
    var adpushup = window.adpushup = window.adpushup || {};
    adpushup.que = adpushup.que || [];
    adpushup.que.push(function() {
      adpushup.triggerAd("13db4aa1-4df7-4402-8d7f-f1ef0360ea7c");
    });
  </script>
</div>
</div>
<div class="clear"></div>
<div class="urls intnl_contr_link_block">
<?php echo '<div class="intnl_heading"><h2>List of Villages in '.$tehsil.', '.$district.', '.$state.'</h2></div>';?>
<div class="vill_liking"><ul>
<?php echo $village_urls;?>
</ul></div>
</div><br />


<?php include 'form.html';?>

</div>

</div>


<div class="bottom_two_ads"><!--space for append addcodes in bottom--></div>



</div>

<div id="right-panel" >

</div>
</div>

<div  class="content-panel">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
<td width="10" class="left_round_corner">&nbsp;</td>
<td class="round_corner_border">&nbsp;</td>
<td width="10" class="right_round_corner">&nbsp;</td>
</tr>
</table>
<br /><br />
</div>
</div>
</div>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
<td width="11" class="left_round_corner1">&nbsp;</td>
<td class="round_corner_border1">&nbsp;</td>
<td width="11" class="right_round_corner1">&nbsp;</td>
</tr>
</table>
<!-- 	left panel starts-->
<?php virtual("/delhi/bottom-links-delhi.html");?>
<!-- 	left panel Ends-->
</div>
<div  class="main bottom-lnk">
<script  >
<!--
bottom_link();
//-->
</script>
</div>
<!-- Footer -->

<div  class="main">
<script language="JavaScript" type="text/javascript">
<!--
footer();
//-->
</script>
</div>


<script type="text/javascript">
var geocoder;
var map;
function initialize() {
geocoder = new google.maps.Geocoder();
var address = '<?php echo $address; ?>';
var latlng;
geocoder.geocode( { 'address': address}, function(results, status) {
if (status == google.maps.GeocoderStatus.OK) 
{
latlng = results[0].geometry.location;
make_map(latlng)		
}
else
{
latlng = new google.maps.LatLng(10, 10);
make_map(latlng)
}
});
}
google.maps.event.addDomListener(window, 'resize', initialize);
google.maps.event.addDomListener(window, 'load', initialize);
function make_map(latlng)
{
var mapOptions = {
zoom: 15,
center: latlng,
mapTypeId: google.maps.MapTypeId.ROADMAP
}
map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
var contentString = '<?php echo $address; ?>';
var infowindow = new google.maps.InfoWindow({
content: contentString
});
var marker = new google.maps.Marker({
map: map,
position: latlng,
title: 'Address'
});
google.maps.event.addListener(marker, 'click', function() {
infowindow.open(map,marker);
});
}
</script>
<script type="text/javascript">
$(document).ready(function(){
$('.show_more').click(function(){
$(this).hide();
$(this).parent().find(".show_div").css({"display":"none"});
$(this).parent().find(".hide_div").css({"display":"block"});
});
});
</script>
 <script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-22494977-1', 'auto');
ga('send', 'pageview');

</script>
</body>
</html>
<?php 
}
else{
virtual('/notfound.html');
}
?>
