<?php
error_reporting(0);
include('moipincode_connection.php');
$state_url=$_REQUEST['state'];
$district_url=$_REQUEST['district'];
$location_url=$_REQUEST['location'];
$state_url = trim(str_replace(array(' ','_'),array('-','-'),strtolower($state_url)));
$district_url = trim(str_replace(array(' ','_'),array('-','-'),strtolower($district_url)));
$location_url = trim(str_replace(array(' ','_'),array('-','-'),strtolower($location_url)));

#echo $query;
$flagOutput = true;
// echo $query ;

$output ="";
if ($location_url!='' || $location_url!=null) {
	$sql_q="SELECT p.pin,p.location,s.state,d.district from pincode p inner join district_location dl on dl.lid=p.id inner join districts d on d.did=dl.did inner join state_district sd on d.did=sd.did inner join states s on s.id=sd.sid where p.search_location='".$location_url."' and d.search_district='".$district_url."' and s.surl ='".$state_url."'";
	$query2 = mysqli_query($db, $sql_q);


	
	if(mysqli_num_rows($query2)>0)
	{
	    $rowpin = mysqli_fetch_assoc($query2);
	    $pin = $rowpin['pin'];
	    $state=ucwords(strtolower($rowpin['state']));
	    $location=ucwords(strtolower($rowpin['location']));
	    $district=ucwords(strtolower($rowpin['district']));
	}
	else
	{
	  header("Location:http://localhost/pincode/");
	  exit();
	}
	if(isset($pin))
	{
        $flagOutput = false;
        $output=$output .'<table>';
        $output=$output .'<tr><th colspan="4" style="text-align:center"><h2>'.$location.' Pincode Details</h2></th></tr>';    
        $output=$output .'<tr>';
        $output=$output .'<td><b>Location</b></td>';
        $output=$output .'<td><b>Pincode </b></td>';
        $output=$output .'<td><b>State </b></td>';
        $output=$output .'<td><b>District </b></td>';
        $output=$output .'</tr>';
        
        $title = "$location Pin Code, $district ($state)";
        $title1 = "$location Pin Code ($district, $state) | $location Postal Index Number Code (Pincode)";
        $keywords = "Pin Code of $location, Pin Code of $location $district $state, Pin Code of $location $state, Pin Code of $location $district ";
        $description = "The Pin Code of $location is $pin. Get more details along with the Pin Code of $location. $location is located in $district district in $state.";
        $output=$output .'<tr>';
        $output=$output .'<td>'.$location.'</td>';
        $output=$output .'<td><b>'.$pin.' </b></td>';
        $output=$output .'<td>'.$state.' </td>';
        $output=$output .'<td>'.$district.' </td>';
        $output=$output .'</tr>';            
        $output=$output .'</table>';
        
        
      $matching_pinquery =  mysqli_query($db,"select id,location,search_location from pincode where pin='".$pin."' and location<>'".$location."'");
        
        if(mysqli_num_rows($matching_pinquery) > 0){

            $output .='<br /><br /> 
                       <table><caption><h2>List of '.$location.' Near By Pin Code Details</h2></caption></table>
                        <div class="table_hide table-responsive">
                        <table class="link_table extrtable">
                        <tr>
                        <td><b>Location</b></td>
                        <td><b>Pincode</b></td>
                        <td><b>State</b></td>
                        <td><b>District</b></td>
                        </tr>';

            while($rslt_pinquery = mysqli_fetch_array($matching_pinquery)){

                $mloc =  $rslt_pinquery['location'];
                $ms_loc =  $rslt_pinquery['search_location'];

                $districtquery = mysqli_query($db,"select did from district_location where lid = ".$rslt_pinquery['id']);
                $rowdistrict = mysqli_fetch_row($districtquery);

                $districtnamequery = mysqli_query($db,"select district,search_district from districts where did=".$rowdistrict[0]);
                $rowdistrictname = mysqli_fetch_row($districtnamequery);
                
                $mdis = $rowdistrictname[0];
                $ms_dis = $rowdistrictname[1];

                $statequery = mysqli_query($db,"select sid from state_district where did =".$rowdistrict[0]);
                $rowstate = mysqli_fetch_row($statequery);

                $statenamequery = mysqli_query($db,"select state,surl from states where id = ". $rowstate[0]);
                $rowstatename = mysqli_fetch_row($statenamequery);

                $mstate = $rowstatename[0];
                $ms_state = $rowstatename[1];
                $cleanurl = '/pincode/india/'.$ms_state.'/'.$ms_dis.'/'.$ms_loc.'.html';
                $output .= '<tr>
                <td><a href="'.($cleanurl).'">'.strProper($mloc).'</a></td>
                <td>'.$pin.'</td>
                <td>'.strProper($mstate).'</td>
                <td>'.strProper($mdis).'</td>
                </tr>';


            }
                             
                             
            $output=$output .'</table></div>';
		}
            
        if ($flagOutput) {
            $output ='';
        }
	}

	
}

function strProper($str) {
    $noUp = array('a','an','of','the','are','at','in');
    $str = trim($str);
    $str = strtoupper($str[0]) . strtolower(substr($str, 1));
    for($i=1; $i<strlen($str)-1; ++$i) {
        if($str[$i]==' ' || $str[$i]=='.') {
            for($j=$i+1; $j<strlen($str) && ($str[$j]!=' ' || $str[$j]!='.'); ++$j); //find next space
            $size = $j-$i-1;
            $shortWord = false;
            if($size<=3) {
                $theWord = substr($str,$i+1,$size);
                for($j=0; $j<count($noUp) && !$shortWord; ++$j)
                    if($theWord==$noUp[$j])
                        $shortWord = true;
            }
            if( !$shortWord )
                $str = substr($str, 0, $i+1) . strtoupper($str[$i+1]) . substr($str, $i+2);
        }
        $i+=$size;
    }
    return $str;
}
$page_url=explode('/',$_SERVER['REQUEST_URI']);
$canonical=$page_url[1].'/'.$page_url[3].'/'.$page_url[4].'/'.$page_url[5];
$amp_link=$page_url[1].'/amp/'.$page_url[3].'/'.$page_url[4].'/'.$page_url[5];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-BXSFPEHNGD"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-BXSFPEHNGD');
</script>

<title><?php echo $title1 ?>  </title>
<meta name="description" content="<?php echo $description ?>" />
<meta name="keywords" content="<?php echo $keywords ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<!-- OG Tags -->
<meta property="og:title" content="<?php echo $title1 ?>" />
<meta property="og:url" content="https://www.mapsofindia.com/<?php echo $canonical;?>" />
<meta property="og:description" content="<?php echo $description ?>" />
<meta property="og:site_name" content="Maps of India" />
<meta property="og:image" content="https://www.mapsofindia.com/elements/img/logo-moi.jpg" />
<meta name="twitter:card" content="photo"/>
<meta name="twitter:site" content="@MapsofIndia"/>
<meta name="twitter:title" content="<?php echo $title1 ?>"/>
<meta name="twitter:description" content="<?php echo $description ?>"/>
<meta name="twitter:image:src" content="https://www.mapsofindia.com/elements/img/logo-moi.jpg"/>
<!-- OG Tags End-->
<link rel="alternate" media="only screen and (max-width:736px)" href="https://m.mapsofindia.com/<?php echo $canonical;?>">
<link rel="amphtml" href="https://m.mapsofindia.com/<?php echo $amp_link;?>">
 <script src="https://www.mapsofindia.com/elements/style.js"></script>
<script language="JavaScript" src="<?php echo $baseUrl; ?>js/pincode.js" type="text/Javascript"></script>
    <link rel="stylesheet" href="https://www.mapsofindia.com/elements/style.css">
        <script>
            var googletag = googletag || {};
            googletag.cmd = googletag.cmd || [];
            (function () {
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

      <!--<script type='text/javascript'>
        googletag.cmd.push(function() {

            if (window.innerWidth > 770) {
                 googletag.defineSlot('/5535731/MDPLleaderboard', [728, 90], 'div-gpt-ad-1715843693925-0').addService(googletag.pubads());
    googletag.defineSlot('/5535731/MDPLLeaderboardmultisizedx90', [[970, 90], [728, 90]], 'div-gpt-ad-1715844019345-0').addService(googletag.pubads());
    googletag.defineSlot('/5535731/MDPL-Left-Gutter-multisized-120X600', [[120, 20], [120, 240], [120, 30], [120, 60], [120, 600]], 'div-gpt-ad-1730784866247-0').addService(googletag.pubads());
    googletag.defineSlot('/5535731/MDPL-Right-gutter-multi-120X600', [[120, 20], [120, 240], [120, 30], [120, 60], [120, 90], [120, 600]], 'div-gpt-ad-1730785180866-0').addService(googletag.pubads());
    googletag.defineSlot('/5535731/MDPL_skyscraper_120x600', [120, 600], 'div-gpt-ad-1715852787291-0').addService(googletag.pubads());
    googletag.defineSlot('/5535731/MDPL_Footerstickyad_multisizedX90', [[970, 90], [728, 90]], 'div-gpt-ad-1715852964252-0').addService(googletag.pubads());
    googletag.defineSlot('/5535731/MDPL_footerad_multisizedX90', [[970, 90], [728, 90]], 'div-gpt-ad-1715853150738-0').addService(googletag.pubads());
            }
            else if (window.innerWidth < 770){
              googletag.defineSlot('/5535731/MDPL_mobile_leaderboard_320X50', [320, 50], 'div-gpt-ad-1715853590763-0').addService(googletag.pubads());
    googletag.defineSlot('/5535731/MDPL_mobile_leaderboard2', [320, 50], 'div-gpt-ad-1715853768570-0').addService(googletag.pubads());
    googletag.defineSlot('/5535731/MDPL_Mobile_stickyfooter320x50', [320, 50], 'div-gpt-ad-1715854037692-0').addService(googletag.pubads());
    googletag.defineSlot('/5535731/MDPL_mobile_middlead_multisizedx250', [[250, 250], [300, 250]], 'div-gpt-ad-1715854203053-0').addService(googletag.pubads());
    googletag.defineSlot('/5535731/MDPL_mobile_Footerad_multisizedx250', [[250, 250], [300, 250]], 'div-gpt-ad-1715854399626-0').addService(googletag.pubads());
            }

       googletag.pubads().enableSingleRequest();
            //googletag.pubads().disableInitialLoad();
            googletag.enableServices();
        });
    </script>-->
    <script src="https://cdn.rediads.com/mapsofindia/js/ads.min.js" type="text/javascript" async ></script>
<link rel="stylesheet" href="https://cdn.rediads.com/css/style.min.css">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8993220319430392"crossorigin="anonymous"></script>

<script data-cfasync="false" type="text/javascript">
(function(w, d) {
	var s = d.createElement('script');
	s.src = '//cdn.adpushup.com/42563/adpushup.js';
	s.crossOrigin='anonymous'; 
	s.type = 'text/javascript'; s.async = true;
	(d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
	w.adpushup = w.adpushup || {que:[]};
})(window, document);

</script>
<style>
.leader-redi-ad{width: 728px; height: 90px; margin: 0 auto;float: right;}
.video-ad{width: 640px;height: 360px;margin: 0 auto;}
.copy.sticky {margin-bottom: 58px;}
@media screen and (max-width:768px){
.leader-redi-ad {width: 375px;height: 50px;text-align: center;}
.video-ad{width: 320px;height: 180px;margin: 0 auto;}
}
</style>

</head>

    <body>
<!-- /5535731/MDPL-Left-Gutter-multisized-120X600 -->
<!--<div class="desktop-leftG">
<div id='div-gpt-ad-1730784866247-0' style='position: fixed;
    z-index: 2000;
    left: 0px;
    margin-left: 5px;
    top: 20.5px;
    width: 120px;
    height: 600px;
    display: block;
    text-align: center;min-width: 120px; min-height: 20px;'>
  <script>
    googletag.cmd.push(function() { googletag.display('div-gpt-ad-1730784866247-0'); });
  </script>
</div>
</div>-->
<!-- /5535731/MDPL-Right-gutter-multi-120X600 -->
<!--<div class="desktop-rightG">
<div id='div-gpt-ad-1730785180866-0' style='right: 0px;
    top: 20.5px;
    width: 120px;
    height: 600px;
    position: fixed;
    pointer-events: none;
    margin-right: 5px;
    z-index: -9999;min-width: 120px; min-height: 20px;'>
  <script>
    googletag.cmd.push(function() { googletag.display('div-gpt-ad-1730785180866-0'); });
  </script>
</div>
</div>-->
       
        <!-- header start here -->
<?php include_once('../../elements/header.html');?>
<!-- End here header -->

        <section>
            <div class="fullwidth top-map-sec"> 
                <div class="breadSearch">
                    <div class="main">
                        <div class="breadcrums">
                            <ul>
                                <li><a href="https://www.mapsofindia.com/">Home >> </a></li>
                                <li><a href="http://localhost/pincode/">Pincode >> </a></li>
                                <?php  echo $breadcurmbs;  ?>
                            </ul>
                        </div>
                        <div class="searchBox">
                            <?php include('../search_city.php');?>
                        </div>  
                        <br><br>
                    <div class="customer-servises" style="margin: 5px 0 0 0;font-family: arial;"><a href="https://www.mapsofindia.com/custom-maps/" style="color:#fff;text-decoration:none;font-weight:600;font-size:12px">For Custom/ Business Map Quote </a>&nbsp;&nbsp; <b style="color:#000;font-size:12px;"> +91 8929683196 | <a href="mailto:apoorv@mappingdigiworld.com">apoorv@mappingdigiworld.com</a></b></div>  
                    </div>
                </div>
                <div class="main">

   
                    <div class="map-sec">
                        <div class="main-map">
                            <div class="dividesection2">
                                <div class="heading-first-pin">
                                <h1><?php echo $title; ?></h1>
                                </div><br><br><br>
                                <div class="heading-sec-pin"><h2><?php echo $location ?> Pin Code is <?php echo $pin ?>. <?php echo $location ?> is located in <?php echo $district ?> in <?php echo $state ?>, India.</h2></div><br>
                                <div class="pin-para">
                                    <p>The Pin Code of <?php echo $location ?> is <?php echo $pin ?>. Get more details along with the Pin Code of <?php echo $location ?>. <?php echo $location ?> is located in <?php echo $district ?> district in <?php echo $state ?>. <?php echo $pin ?> is the pincode (Postal code) of <?php echo $location ?>.</p>
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
<!-- /5535731/MDPL_mobile_middlead_multisizedx250 -->
<!--<div class="mobile-middle">
<div id='div-gpt-ad-1715854203053-0' style='min-width: 250px; min-height: 250px;'>
  <script>
    googletag.cmd.push(function() { googletag.display('div-gpt-ad-1715854203053-0'); });
  </script>
</div>
</div>-->
                               <div class="social-links">
                                        <a rel="nofollow" href="https://www.facebook.com/mapsofindia"><img src="https://www.mapsofindia.com/elements/img/fb-icon.jpg" alt="FB icon"  width="22" height="21"  /></a>
                                        <a rel="nofollow" href="https://twitter.com/mapsofindia"><img src="https://www.mapsofindia.com/elements/img/twitter-icon.jpg" alt="Twitter icon"   width="22" height="21"/></a>
                                        <a rel="nofollow" href="https://www.pinterest.com/mapsofindia"><img src="https://www.mapsofindia.com/elements/img/pinterest-icon.jpg" alt="Pinterest icon" width="22" height="21"/></a>
                                       
                                    </div>
                                <div class="tables table2 sliderespon">
                                    <?php echo $output; ?>
                      </div> 


                                <div class="railImg3">
                                    <img src="https://www.mapsofindia.com/elements/img/Pincode-Bar.png" alt="icon" width="800" height="50" loading="lazy">
                                    <h3>PIN CODE SEARCH TOOL</h3>
                                </div>
                                <div class="main_3">
                                    <div class="form_left">
                                        <div class="formBox">
                                            <form name="frmPin" method='post' action="pinresult1.php" onSubmit="return validate();">
                                            <input type="hidden" name="flag" value="f"/>
                                            <div class="dropp">
                                                <select id="stateSelect" name="dropOption" class="dropOption selectHide" onchange="getDistrictName(this.value)" >
                                                    <option selected="selected">Select State </option>
                                                </select>
                                            </div>
                                            <div class="dropp">
                                                <select id="districtSelect" name="dropOption" class="dropOption selectHide" onchange="getLocName(this.form.stateSelect.value,this.value)">
                                                    <option selected="selected">Select District</option>
                                                </select>
                                            </div>
                                            <div class="dropp">
                                                <select id="locSelect" name="dropOption" class="dropOption selectHide">
                                                    <option value="1" selected="selected">Select City</option>
                                                </select>
                                            </div>
                                            <div id="captcha1"></div>
                                            <center><button type="button" class="submitBtn" onclick="return validate();" value='Search' name="pinSearch">Search</button></center>
                                        </form>
                                        <script>
                                            var captcha1WidgetId;
                                            var captcha2WidgetId;

                                            function onloadCallback() {
                                                captcha1WidgetId = grecaptcha.render('captcha1', {
                                                    'sitekey': '6LcZpgwrAAAAAKbejTbeT1MvFCr64SeJruoZmx3I'
                                                });

                                                captcha2WidgetId = grecaptcha.render('captcha2', {
                                                    'sitekey': '6LcZpgwrAAAAAKbejTbeT1MvFCr64SeJruoZmx3I'
                                                });
                                            }
                                        </script>

                                        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>

                                        </form>
                                        </div>
                                    </div>
                                    <div class="linemiddle">
                                        <div class="linetext">(Or)</div>
                                        <div class="vrline"></div>
                                    </div>
                                    <div class="form_right">
                                        <div class="secondform">
                                        <p>Enter Pincode to Search Post Office</p><br>
                                        <form name="frmLoc" method='post' action="">
                                            <input type="text" name='txtPin' id='txtPin' size="30" maxlength="6" /><br>
                                            <div id="captcha2"></div>
                                            <center><button type="button" class="searchBtn" name="locSearch" value='  Search  ' onClick="return redirectpincode();">Search</button></center>
                                        </form>
                                        <script>
                                            var captcha1WidgetId;
                                            var captcha2WidgetId;

                                            function onloadCallback() {
                                                captcha1WidgetId = grecaptcha.render('captcha1', {
                                                    'sitekey': '6LcZpgwrAAAAAKbejTbeT1MvFCr64SeJruoZmx3I'
                                                });

                                                captcha2WidgetId = grecaptcha.render('captcha2', {
                                                    'sitekey': '6LcZpgwrAAAAAKbejTbeT1MvFCr64SeJruoZmx3I'
                                                });
                                            }
                                        </script>

                                        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
                                    </div>
                                    </div>
                                </div>
                                <div class="socialBox socialBox2">
                                    <p>Share This On</p>
                                    <ul>
                                        <li><a class="pin-it-button addthis_button_pinterest_share at300b" href="#"><img src="https://www.mapsofindia.com/elements/img/Pinterest.png" alt="icon" width="32" height="32" loading="lazy"></a></li>
                                        <!-- <li><a href="#"><img src="<?php echo $baseUrl;?>img/Google.png" alt="icon"></a></li>
                                        <li><a href="#"><img src="<?php echo $baseUrl;?>img/Linkedin.png" alt="icon"></a></li> -->
                                        <li><a class="addthis_button_facebook at300b" href="#"><img src="https://www.mapsofindia.com/elements/img/Facebook.png" alt="icon" width="32" height="32" loading="lazy"></a></li>
                                        <li><a class="addthis_button_twitter at300b" href="#"><img src="https://www.mapsofindia.com/elements/img/Twitter.png" alt="icon" width="32" height="32" loading="lazy"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <!-- ======= -->
                            <div class="text">
                                
                                <div class="tables">
                                   
                       
                                    <span class="subHeading2">Last Update on: April 13, 2021 </span><br><br>
                                    <span class="span-tg">Buy India Maps Online</span>

                    <div class="sliderespon">
                        <div class="mapsList">
                            <a href="https://store.mapsofindia.com/digital-maps/country-maps-1-2-3/india/indian-railways-map">
                                <div class="mapsCard">
                                    <div class="mapImg"><img src="https://www.mapsofindia.com/elements/img/Thumbnail-1.jpg" alt="icon"  width="126" height="125" loading="lazy"/></div>
                                    <div class="maptext">
                                        <div class="lefttext"> India Railway Map</div>
                                        <div class="righttext">Rs.999.00</div>
                                    </div>
                                    <div class="bottomlogo"><img src="https://www.mapsofindia.com/elements/img/logo-moi.jpg" alt="icon" width="70" height="12" loading="lazy"/></div>
                                </div>
                            </a>
                            <a href="https://store.mapsofindia.com/digital-maps/country-maps-1-2-3/india/indian-railway-zonal-map">
                                <div class="mapsCard">
                                    <div class="mapImg"><img src="https://www.mapsofindia.com/elements/img/Thumbnail-2.jpg" alt="icon"  width="126" height="125" loading="lazy"/></div>
                                    <div class="maptext">
                                        <div class="lefttext">Indian Railway Zonal Map </div>
                                        <div class="righttext">Rs.999.00</div>
                                    </div>
                                    <div class="bottomlogo"><img src="https://www.mapsofindia.com/elements/img/logo-moi.jpg" alt="icon" width="70" height="12" loading="lazy"/></div>
                                </div>
                            </a>
                            <a href=" https://store.mapsofindia.com/digital-maps/railway-maps/indian-railway-electrification-map-2">
                                <div class="mapsCard">
                                    <div class="mapImg"><img src="https://www.mapsofindia.com/elements/img/Thumbnail-3.jpg" alt="icon"  width="126" height="125" loading="lazy"/></div>
                                    <div class="maptext">
                                        <div class="lefttext"> Indian Railway Electrification Map </div>
                                        <div class="righttext">Rs.999.00</div>
                                    </div>
                                    <div class="bottomlogo"><img src="https://www.mapsofindia.com/elements/img/logo-moi.jpg" alt="icon" width="70" height="12" loading="lazy"/></div>
                                </div>
                            </a>
                            <a href="https://store.mapsofindia.com/digital-maps/country-maps-1-2-3/india/railway-maps/uttar-pradesh-railway-map">
                                <div class="mapsCard">
                                    <div class="mapImg"><img src="https://www.mapsofindia.com/elements/img/Thumbnail-4.jpg" alt="icon"  width="126" height="125" loading="lazy"/></div>
                                    <div class="maptext">
                                        <div class="lefttext"> Uttar Pradesh Railway Map</div>
                                        <div class="righttext">Rs.1499.00</div>
                                    </div>
                                    <div class="bottomlogo"><img src="https://www.mapsofindia.com/elements/img/logo-moi.jpg" alt="icon" width="70" height="12" loading="lazy"/></div>
                                </div>
                            </a>
                        </div>
                    </div>
                                    <div class="contactBox">
                                        <div class="addressPart">
                                            <span class="span-txt">for inquiries on Custom Mapping and  Map Based Application</span><br>
                                            <div class="picText">
                                                <div class="pic"><img src="https://www.mapsofindia.com/style_2019/images/apoorv.jpg" alt="icon" width="95" height="90" loading="lazy"/></div>
                                                <div class="text2">
                                                    Apoorv<br>
                                                    Mapping Consultant<br>
                                                    +91 8929683196 (IST) | apoorv@mappingdigiworld.com<br>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="imgPart"><img src="https://www.mapsofindia.com/elements/img/Mapience.png" alt="icon" width="135" height="99" loading="lazy"/></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ========== right panel ============= -->
                        <div class="map-asider">



                           <div class="bootomGap">     
                <div class="underLine hd4"><a href="https://www.mapsofindia.com/general/" title="Maps of India">Maps of India</a></div>
                <div class="list hd_show4">
                    <ul>
                       <li><a href="https://www.mapsofindia.com/maps/india/india-political-map.htm" title="Map of India">Map of India</a></li>
<li><a href="https://www.mapsofindia.com/maps/india/physical-map.html" title="Physical Map of India">Physical Map of India</a></li>
<li><a href="https://www.mapsofindia.com/maps/india/outlinemapofindia.htm" title="Outline Map of India">Outline Map of India</a></li>
                    </ul>
                </div>
            </div>

            <div class="bootomGap">
                <div class="underLine hd4"><a href="https://www.mapsofindia.com/reference-maps/" title="India Maps And Views">India Maps And Views </a>
</div>
                <div class="list hd_show4">
                    <ul>
                        <li><a href="https://www.mapsofindia.com/geography/" title="India Geography">India Geography</a></li>
<li><a href="https://www.mapsofindia.com/history/" title="History of India">History of India</a></li>
<li><a href="https://www.mapsofindia.com/census/" title="India Census Maps">India Census Maps</a></li>
<li><a href="https://www.mapsofindia.com/economy/" title="Business and Economy">Business and Economy</a></li>
<li><a href="https://www.mapsofindia.com/culture/" title="India Culture">India Culture</a></li>
                    </ul>
                </div>
            </div>
            <div class="bootomGap">
                <div class="underLine hd4"><a href="https://www.mapsofindia.com/infrastructure/" title="Transport Network In India">Transport Network In India </a></div>
                <div class="list hd_show4">
                    <ul>
                       <li><a href="https://www.mapsofindia.com/roads/" title="Road Map of India">Road Map of India</a></li>
<li><a href="https://www.mapsofindia.com/railways/" title="India Rail Network">India Rail Network</a></li>
<li><a href="https://www.mapsofindia.com/air-network/" title="Air Network in India">Air Network in India</a></li>
<li><a href="https://www.mapsofindia.com/water/" title="Ports in India &amp; waterways">Ports in India &amp; waterways</a></li>
                    </ul>
                </div>
            </div>


<div class="bootomGap">
                <div class="underLine"><a href="https://www.mapsofindia.com/tourism/" title="India Travel">India Travel </a></div>
</div>

            <div class="bootomGap">
                <div class="underLine hd4"><a href="https://www.mapsofindia.com/states/" title="India States &amp; Union Territories">India States &amp; Union Territories</a></div>
                <div class="list hd_show4">
                    <ul>
                       <li><a href="https://www.mapsofindia.com/stateprofiles/" title="State Profiles">State Profiles</a></li>
<li><a href="https://www.mapsofindia.com/delhi/">Delhi</a></li>
<li><a href="https://www.mapsofindia.com/kerala/" title="Kerala">Kerala</a></li>
<li><a href="https://www.mapsofindia.com/tamilnadu/" title="Tamil Nadu">Tamil Nadu</a></li>
<li><a href="https://www.mapsofindia.com/gujarat/" title="Gujarat">Gujarat</a></li>
<li><a href="https://www.mapsofindia.com/rajasthan/" title="Rajasthan">Rajasthan</a></li>
                    </ul>
                </div>
            </div>



<div class="bootomGap">
                <div class="underLine hd4"><a href="https://www.mapsofindia.com/maps/cities/" title="Cities Of India">Cities Of India</a></div>
                <div class="list hd_show4">
                    <ul>
                      <li><a href="https://www.mapsofindia.com/top-ten-cities-of-india/" title="Top 10 Cities of India">Top 10 Cities of India</a></li>
<li><a href="https://www.mapsofindia.com/maps/karnataka/bangalore-map.htm" title="Bangalore">Bangalore</a></li>
<li><a href="https://www.mapsofindia.com/maps/mumbai/" title="Mumbai">Mumbai</a></li>
<li><a href="https://www.mapsofindia.com/maps/tamilnadu/chennai-map.htm" title="Chennai">Chennai</a></li>
                    </ul>
                </div>
            </div>

<div class="bootomGap">
                <div class="underLine hd4"><a href="https://www.mapsofindia.com/roads/" title="Driving Direction Maps">Driving Direction Maps </a></div>
                <div class="list hd_show4">
                    <ul>
                      <li><a href="https://www.mapsofindia.com/driving-directions-maps/" title="National Highways">National Highways</a></li>
<li><a href="https://www.mapsofindia.com/maps/pocketmaps/" title="Intra City Maps">Intra City Maps</a></li>
                    </ul>
                </div>
            </div>



<div class="bootomGap">
                <div class="underLine"><a href="https://www.mapsofindia.com/world-map/" title="World Map">World Map</a></div>
</div>

<div class="bootomGap">
                <div class="underLine"><a href="https://www.mapsofindia.com/world-map/us-map-states-and-capitals.html" title="USA Map">USA Map</a></div>
</div>


			<div class="bootomGap">
                <div class="underLine"><a href="https://www.mapsofindia.com/utilities/" title="Utilities">Utilities</a></div>
           </div>
                        </div>
                    </div>
                    <!-- =========== end right panel ======== -->
                </div>
            </div> 
        </section>
<!-- my india section start here -->
<section>
    <div class="main">
        <div class="fullwidth myindia-sec">
            <a href="https://www.mapsofindia.com/my-india/"><span class="span-tg">My India</span></a><br>
          	<?php echo file_get_contents('https://www.mapsofindia.com/my-india/files/myindiawpposts.php');?>
        </div>       
    </div>
</section>
<!-- End here my india section  -->

<!-- ==================== foooter start here ================== -->
    <?php include_once('../../elements/footer.html');?>
<!-- ================ footer End =================== -->
</body>

</html>
