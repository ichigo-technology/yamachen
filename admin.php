<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', getenv('PHPFMG_USER') ?: '' );
define( 'PHPFMG_PW', getenv('PHPFMG_PW') ?: '' );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'86AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQximMIY6IImJTGFtZQhldAhAEgtoFWlkdHR0EEFRJ9LA2hAIEwM7aWnUtLClqyKzpiG5T2SKaCuSOrh5rqFYxNDUgdwC0ovsFpCbgWIobh6o8KMixOI+AJkkzAfs6DBrAAAAAElFTkSuQmCC',
			'542F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYWhlCGUNDkMQCGhimMjo6OjCgioWyNgSiiAUGMLoyIMTATgqbtnTpqpWZoVnI7msVaWVoZUTRy9AqGuowBVUsAKiKIQBVTGQKSCeqGGsAQytrKKpbBir8qAixuA8ATmvIy2wAEkYAAAAASUVORK5CYII=',
			'50CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMYAhhCHVqRxQIaGEMYHQKmOqCIsbayNggEBCCJBQaINLo2MDqIILkvbNq0lamrVmZNQ3ZfK4o6ZLHQEGQ7WkF2CKKoE5kCcksgihhrAMjNjqjmDVD4URFicR8AQ1DLI/F7+6gAAAAASUVORK5CYII=',
			'FC60' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZQxlCGVqRxQIaWBsdHR2mOqCIiTS4NjgEBKCJsTYwOogguS80atqqpVNXZk1Dch9YHdBAEQy9gRhirg0BaHZgcwummwcq/KgIsbgPAFm+zf724m3iAAAAAElFTkSuQmCC',
			'D7A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMDQEIIkFTGFodAhlaEQRa2VodHR0aEUTa2UFqg5Acl/U0lXTlq6KiopCch9QXQBrQ6ADql5GB9bQwNAQFDHWBqB5aG4RwRALDcAUG6jwoyLE4j4APgLQVJsCOvAAAAAASUVORK5CYII=',
			'C4C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WEMYWhlCHVqRxURaGaYyOgRMRRYLaGQIZW0QCEURa2B0ZW1ggOkFOylq1dKlS1etWorsvgCgiUjqoGKioa7oYo0MQHUC6G5pBboFRQzq5tCAQRB+VIRY3AcA667MEEv0D5QAAAAASUVORK5CYII=',
			'0844' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHRoCkMRYA1hbGVodGpHFRKaINDpMdWhFFgtoBaoLdJgSgOS+qKUrw1ZmZkVFIbkPpI610dEBVa9Io2toYGgIuh3Y3IImhs3NAxV+VIRY3AcA3+POaesPaHEAAAAASUVORK5CYII=',
			'D6E0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHVqRxQKmsLayNjBMdUAWaxVpBIoFBKCKNbA2MDqIILkvaum0sKWhK7OmIbkvoFW0FUkd3DxXrGJodmBxCzY3D1T4URFicR8ALM3NCR310VUAAAAASUVORK5CYII=',
			'230A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANYQximMLQii4lMEWllCGWY6oAkFtDK0Ojo6BAQgKy7laGVtSHQQQTZfdNWhS1dFZk1Ddl9ASjqwJDRgaHRtSEwNATZLQ0gOxxR1Ik0gNzCiCIWGgpyM6rYQIUfFSEW9wEAgbbKsaYC0ZkAAAAASUVORK5CYII=',
			'372F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RANEQx1CGUNDkMQCpjA0Ojo6OqCobGVodG0IRBWbAhRFiIGdtDJq1bRVKzNDs5DdN4UhgKGVEc08IH8KuhhrA0MAqljAFJEGRgdUMdEAkQbWUDS3DFD4URFicR8AVNfIw4ySDZAAAAAASUVORK5CYII=',
			'B3D2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDGaY6IIkFTBFpZW10CAhAFmtlaHRtCHQQQVHH0MraENAgguS+0KhVYUtXRQEhwn1QdY0OGOYBSUyxKQxY3ILpZsbQkEEQflSEWNwHAHyizsyk3CpDAAAAAElFTkSuQmCC',
			'9C94' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGRoCkMREprA2Ojo6NCKLBbSKNLgCSXQx1oaAKQFI7ps2ddqqlZlRUVFI7mN1FWlgCAl0QNbLANTL0BAYGoIkJgAUcwS6BItbUMSwuXmgwo+KEIv7AKn/zhxJDIp6AAAAAElFTkSuQmCC',
			'EF9D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUMdkMQCGkQaGB0dHQLQxFgbAh1EcIuBnRQaNTVsZWZk1jQk94HUMYRg6mXAYh4jNjE0t4SGAFWguXmgwo+KEIv7ADR9zDA3ZVq2AAAAAElFTkSuQmCC',
			'AA99' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaY6IImxBjCGMDo6BAQgiYlMYW1lbQh0EEESC2gVaXRFiIGdFLV02srMzKioMCT3gdQ5hARMRdYbGioa6tAQ0IBunmNDAIYdjmhuAZuH5uaBCj8qQizuAwCxNM06teUC5gAAAABJRU5ErkJggg==',
			'F494' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZWhlCGRoCkMSA7KmMjg6NaGKhrA0BrahijK5AsSkBSO4LjVq6dGVmVFQUkvsCGkRaGUICHVD1ioY6NASGhqDa0coIJAPQxRwdMMTQ3TxQ4UdFiMV9ADQVzrdUvZSRAAAAAElFTkSuQmCC',
			'8867' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUNDkMREprC2Mjo6NIggiQW0ijS6NqCKgdSxguSQ3Lc0amXY0qmrVmYhuQ+sztGhlQHDvIApWMQCGDDc4uiAxc0oYgMVflSEWNwHADx4zA/PZWTFAAAAAElFTkSuQmCC',
			'3746' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7RANEQx0aHaY6IIkFTGFodGh1CAhAVtkKFJvq6CCALDYFKBro6IDsvpVRq6atzMxMzUJ23xSGANZGRzTzGB1YQwMdRFDEWBsYGh1RxAKmAHmNqG4RDQCLobh5oMKPihCL+wCnScx2KGYn3AAAAABJRU5ErkJggg==',
			'6A13' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIQ6IImJTGEMYQhhdAhAEgtoYW0FijaIIIs1iDQ6TAHRCPdFRk1bmTVt1dIsJPeFTEFRB9HbKhoKEkMxrxWiTgTFLSAxVLewBog0OoY6oLh5oMKPihCL+wBbzM2HGntasgAAAABJRU5ErkJggg==',
			'5E98' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGaY6IIkFNIg0MDo6BASgibE2BDqIIIkFBoDEAmDqwE4KmzY1bGVm1NQsZPe1AnWFBKCYBxZDMy8AKMaIJiYyBdMtrAGYbh6o8KMixOI+ABW8y/lINmQmAAAAAElFTkSuQmCC',
			'606A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGVqRxUSmMIYwOjpMdUASC2hhbWVtcAgIQBZrEGl0bWB0EEFyX2TUtJWpU1dmTUNyX8gUoDpHR5g6iN5WkN7A0BAUMZAdgSjqIG5B1QtxMyOK2ECFHxUhFvcBAFNNy2ImnQJ1AAAAAElFTkSuQmCC',
			'ED0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNEQximMIaGIIkFNIi0MoQyOjCgijU6OjpiiLk2BMLEwE4KjZq2MnVVZGgWkvvQ1OEVw2IHhlugbkYRG6jwoyLE4j4Ak4TLlgkcja4AAAAASUVORK5CYII=',
			'779E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGUMDkEVbGRodHR0dGNDEXBsCUcWmMLSyIsQgbopaNW1lZmRoFpL7GB0YAhhCUPWygkTRzBMBiaKJBQBFGdHcAhJjQHfzAIUfFSEW9wEAAMnJmoQiEFsAAAAASUVORK5CYII=',
			'03FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDA0NDkMRYA0RaWYEyyOpEpjA0uqKJBbQyIKsDOylq6aqwpaErQ7OQ3IemDiaGYR42O7C5BexmNLGBCj8qQizuAwCTO8hcfpC2mwAAAABJRU5ErkJggg==',
			'7B18' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNFQximMEx1QBZtFWllCGEICEAVa3QMYXQQQRabAlQ3Ba4O4qaoqWGrpq2amoXkPqAuZHVgyNog0ugwBdU8ESxiAQ2YegMaREMYQx1Q3TxA4UdFiMV9AKuKy/rVqULnAAAAAElFTkSuQmCC',
			'BFC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQx1CHUNDkMQCpog0MDoENIggi7WKNLA2CKCKTQGJAWkk94VGTQ1bumrVyiwk90HVtTJgmMcwBVNMIIABwy2BDqhuBroi1BFFbKDCj4oQi/sAva/NJsZck4gAAAAASUVORK5CYII=',
			'0C47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxkaHUNDkMRYA1gbHVodGkSQxESmiDQ4TEUVC2gF8gIdGgKQ3Be1dNqqlZlZK7OQ3AdSBzKRAU0va2jAFAZ0OxodAhjQ3dLo6IDFzShiAxV+VIRY3AcATy3M3OE1lpIAAAAASUVORK5CYII=',
			'9B02' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQximMEx1QBITmSLSyhDKEBCAJBbQKtLo6OjoIIIq1sraENAgguS+aVOnhi1dFQWECPexuoLVNSLbwQA0zxVoArJbBMB2OExhwOIWTDczhoYMgvCjIsTiPgB8msxS/Hz5dgAAAABJRU5ErkJggg==',
			'A378' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDA6Y6IImxBoi0MjQEBAQgiYlMYWh0aAh0EEESC2hlaAWKwtSBnRS1dFXYqqWrpmYhuQ+sbgoDinmhoSCdjOjmNTo6oIuJtLI2oOoNaAW6uYEBxc0DFX5UhFjcBwACPsznsRw3kwAAAABJRU5ErkJggg==',
			'241D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYWhmmMIY6IImJTGGYyhDC6BCAJBbQyhDKCBQTQdbdyugK1AsTg7hp2tKlq6atzJqG7L4AkVYkdWDI6CAa6oAmxtrAgKFOBCqG7JbQUKDNoY4obh6o8KMixOI+ANJVyZ7Ud5mlAAAAAElFTkSuQmCC',
			'BD8F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGUNDkMQCpoi0Mjo6OiCrC2gVaXRtCEQVmyLS6IhQB3ZSaNS0lVmhK0OzkNyHpg63edjtwHAL1M0oYgMVflSEWNwHAM4iy8n+1YK3AAAAAElFTkSuQmCC',
			'CD50' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WENEQ1hDHVqRxURaRVpZGximOiCJBTSKNLo2MAQEIIs1AMWmMjqIILkvatW0lamZmVnTkNwHUufQEAhTh1sMbEcAih0gtzA6OqC4BeRmhlAGFDcPVPhREWJxHwCr8c1lD1lf/AAAAABJRU5ErkJggg==',
			'E378' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDA6Y6IIkFNIi0AsmAABQxhkaHhkAHEVSxVqAoTB3YSaFRq8JWLV01NQvJfWB1UxgwzQtgRDev0dEBXUyklbUBVS/YzQ0MKG4eqPCjIsTiPgAmIM2I+6uD9gAAAABJRU5ErkJggg==',
			'AC54' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDHRoCkMRYA1gbXRsYGpHFRKaINADFWpHFAlpFGlinMkwJQHJf1NJpq5ZmZkVFIbkPpI6hIdABWW9oKFgsNATNPFegS1DtYG10dHRAE2MMZQhlQBEbqPCjIsTiPgBCVc7y51rXyQAAAABJRU5ErkJggg==',
			'12B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGaY6IImxOrC2sjY6BAQgiYk6iDS6NgQ6CKDoZWh0bXR0QHbfyqxVS5eGrkzNQnIfUN0U1kZHFPOAYgGsQPNEUN3igCnG2oDhlhDRUFc0Nw9U+FERYnEfAMmMyYOfMWygAAAAAElFTkSuQmCC',
			'5F82' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGaY6IIkFNIg0MDo6BASgibE2BDqIIIkFBoDVNYgguS9s2tSwVaGrVkUhu68VrK4R2Q6QGGtDQCuyWwIgYlOQxUSmQNyCLMYKtJchlDE0ZBCEHxUhFvcBAIKqzEKHMkHWAAAAAElFTkSuQmCC',
			'BC16' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYQxmmMEx1QBILmMLa6BDCEBCALNYq0uAYwugggKJOpIFhCqMDsvtCo6atWjVtZWoWkvug6jDMA+kVQRNzQBcDuWUKqltAbmYMdUBx80CFHxUhFvcBAJGzzWXlSA0vAAAAAElFTkSuQmCC',
			'D2D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGUNDkMQCprC2sjY6NIggi7WKNLo2BKCJMYDFApDcF7V01dKlq6JWZiG5D6huCiuIRNUbABSbgirG6AAUC2BAdUsDa6OjA6qbRUNdQxlRxAYq/KgIsbgPADSgzk8BU3FaAAAAAElFTkSuQmCC',
			'86C7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHUNDkMREprC2MjoENIggiQW0ijSyNgigiIlMEWlgBckhuW9p1LSwpatWrcxCcp/IFNFWoLpWBjTzXBsYpmCKCQQwYLgl0AGLm1HEBir8qAixuA8A5NTLyGGioD8AAAAASUVORK5CYII=',
			'3FEE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUElEQVR4nGNYhQEaGAYTpIn7RANEQ11DHUMDkMQCpog0sDYwOqCobMUihqoO7KSVUVPDloauDM1Cdh+x5mERw+YW0QCgGJqbByr8qAixuA8AXmfJGE41aY0AAAAASUVORK5CYII=',
			'AB9D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGUMdkMRYA0RaGR0dHQKQxESmiDS6NgQ6iCCJBbSKtLIixMBOilo6NWxlZmTWNCT3gdQxhKDqDQ0VaXTANK/REYsd6G4JaMV080CFHxUhFvcBAIdRy/ouIro2AAAAAElFTkSuQmCC',
			'1BBC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGaYGIImxOoi0sjY6BIggiYk6iDS6NgQ6sKDoBalzdEB238qsqWFLQ1dmIbsPTR1MDGweNjFMO9DcEoLp5oEKPypCLO4DAJwPyWjK3cftAAAAAElFTkSuQmCC',
			'BD47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQxgaHUNDkMQCpoi0MrQ6NIggi7WKNDpMRRObAhQLdGgIQHJfaNS0lZmZWSuzkNwHUufa6NDKgGaea2jAFHQxh0aHAAZ0tzQ6OmBxM4rYQIUfFSEW9wEARkPPOkx5I98AAAAASUVORK5CYII=',
			'101C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YAhimMEwNQBJjdWAMYQhhCBBBEhN1YG1lDGF0YEHRK9LoMAVoApL7VmZNAyNk96GpwyPG2sowBd0OoFumoLkF6DbGUAcUNw9U+FERYnEfAPSxx1aCR7RgAAAAAElFTkSuQmCC',
			'BD21' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGVqRxQKmiLQyOjpMRRFrFWl0bQgIRVPX6ACUQXZfaNS0lVkrs5Yiuw+srhXNDqB5DlOwiAVgcYsDqhjIzayhAaEBgyD8qAixuA8Ag4/OCGI3kAwAAAAASUVORK5CYII=',
			'2A34' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGRoCkMREpjCGsDY6NCKLBbSytoJIZDGGVhGgKocpAcjumzZtZdbUVVFRyO4LAKlzdEDWy+ggGurQEBgaguyWBqA6oEtQ3AIUcwWLIsRCQ0UaHdHcPFDhR0WIxX0AUsfPA0eOFHQAAAAASUVORK5CYII=',
			'3BB2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7RANEQ1hDGaY6IIkFTBFpZW10CAhAVtkq0ujaEOgggiwGUdcgguS+lVFTw5aGrloVhew+iLpGBwzzAloZMMWmMGBxC6abGUNDBkH4URFicR8ArdXNXMfCEH4AAAAASUVORK5CYII=',
			'B18D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUMdkMQCpjAGMDo6OgQgi7WyBrA2BDqIoKhjAKsTQXJfaNSqqFWhK7OmIbkPTR3UPAZM87CJQfUiuyU0gDUU3c0DFX5UhFjcBwByr8oHy3kzeQAAAABJRU5ErkJggg==',
			'A6A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB0YQximMIaGIImxBrC2MoQyNIggiYlMEWlkdHRAEQtoFWlgbQgAQoT7opZOC1u6KmplFpL7AlpFW4HqWpHtDQ0VaXQNDZjCgGpeo2tDQACqGCtQb6ADqhhjCLrYQIUfFSEW9wEAv2bM+cRKcb0AAAAASUVORK5CYII=',
			'4B4C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37poiGMDQ6TA1AFgsRaWVodQgQQRJjDBEBqnJ0YEESY50CVBfo6IDsvmnTpoatzMzMQnZfAFAdayNcHRiGhoo0uoYGOqC6BWhHI6odDCA7GlHdgtXNAxV+1INY3AcAHXfMRAscxdcAAAAASUVORK5CYII=',
			'195E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUMDkMRYHVhbWYEyyOpEHUQaXdHEGEFiU+FiYCetzFq6NDUzMzQLyX1AOwIdGgLR9DI0YoqxAO1AF2NtZXR0RHVLCGMIQygjipsHKvyoCLG4DwABD8dE5HUwOgAAAABJRU5ErkJggg==',
			'59D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGRoCkMQCGlhbWRsdGlHFRBpdGwJakcUCA8BiUwKQ3Bc2benS1FVRUVHI7mtlDHRtCHRA1svQygDUGxgagmxHKwvIPBS3iEwBuwVFjDUA080DFX5UhFjcBwBA589S/ZV4wQAAAABJRU5ErkJggg==',
			'64BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYWllDGUMdkMREpjBMZW10dAhAEgtoYQhlbQh0EEEWa2B0BakTQXJfZNTSpUtDV2ZNQ3JfyBSRViR1EL2toqGu6Oa1At2CJgZ0Syu6W7C5eaDCj4oQi/sAt1fL5HoEp3MAAAAASUVORK5CYII=',
			'6742' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QwQ2AIAxF64EN6j5lg5rAQTbQKSCRDcAdZErhVqJHTei/veTnvxTK4zyMlF/8FM+WAmUSDBMEisQsGB+VZU0omYcIC3kUfqsr57XtxQk/k4BVqCuyGydSliN0TPm6kqBzwca4d25MWzPA/z7Mi98NQ9vNrnEOLQcAAAAASUVORK5CYII=',
			'0449' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YWhkaHaY6IImxBjBMZWh1CAhAEhOZwhDKMNXRQQRJLKCV0ZUhEC4GdlLU0qVLV2ZmRYUhuS+gVaSVFWgHql7RUNfQgAYRVDtAbkGxA+gWkBiKW7C5eaDCj4oQi/sA1OHMBqXbykEAAAAASUVORK5CYII=',
			'E90E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMYQximMIYGIIkFNLC2MoQyOjCgiIk0Ojo6Yoi5NgTCxMBOCo1aujR1VWRoFpL7AhoYA5HUQcUYGjHFWLDYgekWbG4eqPCjIsTiPgAjX8tVbSHpNQAAAABJRU5ErkJggg==',
			'C5D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WENEQ1lDGRoCkMREWkUaWBsdGpHFAhqBYg0BrShiDSIhQLEpAUjui1o1denSVVFRUUjuA8o3ujYEOqDqBYuFhqDaARQLQHMLayvQLShirCGMIehuHqjwoyLE4j4ANOTPehH0nzAAAAAASUVORK5CYII=',
			'4D25' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpI37poiGMIQyhgYgi4WItDI6Ojogq2MMEWl0bQhEEWOdItLo0BDo6oDkvmnTpq3MWpkZFYXkvgCQulaGBhEkvaGhQLEpqGIMIHUBjA5oYq2MDgwBKO4Dupk1NGCqw2AIP+pBLO4DADwdy6MF+MkWAAAAAElFTkSuQmCC',
			'00E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHaY6IImxBjCGsDYwBAQgiYlMYW1lBaoWQRILaBVpdAXJIbkvaum0lamhQBrJfVB1jQ6YelsZMOxgmMKAxS2YbnYMDRkE4UdFiMV9AIeAytIA5gXaAAAAAElFTkSuQmCC',
			'2EDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUMdkMREpog0sDY6OgQgiQW0AsUaAh1EkHWjikHcNG1q2NJVkVnTkN0XgKmX0QFTjLUBU0ykAdMtoaGYbh6o8KMixOI+ALzAywn2KtXAAAAAAElFTkSuQmCC',
			'7854' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHRoCkEVbWVtZGxgaUcVEGl2BJIrYFKC6qQxTApDdF7UybGlmVlQUkvsYHVhbGRoCHZD1sjaINDo0BIaGIImJNIDsCEBxS0ADayujowOaGGMIQygDqpsHKPyoCLG4DwDGKs2c770rFgAAAABJRU5ErkJggg==',
			'D339' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgNYQxhDGaY6IIkFTBFpZW10CAhAFmtlaHRoCHQQQRUDijrCxMBOilq6KmzV1FVRYUjug6hzmCqCYV5AAxYxVDuwuAWbmwcq/KgIsbgPACPfzp6XXrAMAAAAAElFTkSuQmCC',
			'F4E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZWllDHaY6IIkFNDBMZW1gCAhAFQtlbWB0EEERY3RFEgM7KTRq6dKloauiwpDcF9Ag0go0byqqXtFQVyCNKsYAUueARQzdLRhuHqjwoyLE4j4AIAbMRYqIlIAAAAAASUVORK5CYII=',
			'9471' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYWllDA1qRxUSmMExlaAiYiiwGVBEKJENRxRhdGRodYHrBTpo2denSVSCI5D5WV5FWhikMKHYwtIqGOgSgigm0MrQyOjCgu6WVtQFVDOzmBobQgEEQflSEWNwHAJjLy34apKqzAAAAAElFTkSuQmCC',
			'BED8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDGaY6IIkFTBFpYG10CAhAFmsFijUEOoigq2sIgKkDOyk0amrY0lVRU7OQ3IemDrd5uOxAcws2Nw9U+FERYnEfAODXzlifFmUcAAAAAElFTkSuQmCC',
			'EECD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QkNEQxlCHUMdkMQCGkQaGB0CHQLQxFgbBB1EMMQYYWJgJ4VGTQ1bumpl1jQk96GpIyCGaQe6W7C5eaDCj4oQi/sAoDjLu7gGao0AAAAASUVORK5CYII=',
			'10D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGRoCkMRYHRhDWBsdGpHFRB1YW1kbAloDUPSKNLo2BEwJQHLfyqxpK1NXRUVFIbkPoi7QAVNvYGgIihjYjgZUdWC3oIiJhmC6eaDCj4oQi/sASNnLjss8CiAAAAAASUVORK5CYII=',
			'752A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGVpRRFtFGhgdHaY6oImxNgQEBCCLTREJYWgIdBBBdl/U1KWrVmZmTUNyH6MDQ6NDKyNMHRiyNgDFpjCGhiCJiTSINDoEoKoLaGAF6kQXYwxhDQ1EERuo8KMixOI+AJ6CytTfTxncAAAAAElFTkSuQmCC',
			'FC4C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMZQxkaHaYGIIkFNLA2OrQ6BIigiIk0OEx1dGBBE2MIdHRAdl9o1LRVKzMzs5DdB1LH2ghXhxALDcQQc2hEtwPolkZ0t2C6eaDCj4oQi/sAd/7ODfLKt+kAAAAASUVORK5CYII=',
			'5BDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDGUMdkMQCGkRaWRsdHQJQxRpdGwIdRJDEAgOA6hBiYCeFTZsatnRVZNY0ZPe1oqiDiWGYF4BFTGQKpltYAzDdPFDhR0WIxX0A4c/Mpf57HFkAAAAASUVORK5CYII=',
			'B34C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgNYQxgaHaYGIIkFTBFpZWh1CBBBFmsFqXJ0YEFRx9DKEOjogOy+0KhVYSszM7OQ3QdSx9oIVwc3zzU0EEPMoRHdDhGQKIpbsLl5oMKPihCL+wDiUM2R7ayoogAAAABJRU5ErkJggg==',
			'21FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA1qRxUSmMAawNjBMdUASC2hlBYkFBCDrbgXqbWB0EEF237RVUUtDV2ZNQ3ZfAIo6MGR0AIuFhiC7pQFTnQgWsdBQ1lB0sYEKPypCLO4DAA33x9y9bsdEAAAAAElFTkSuQmCC',
			'3C9B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RAMYQxlCGUMdkMQCprA2Ojo6OgQgq2wVaXBtCHQQQRabItLAChQLQHLfyqhpq1ZmRoZmIbsPqI4hJBDDPAZ084Bijmhi2NyCzc0DFX5UhFjcBwCJk8upD+IjFAAAAABJRU5ErkJggg==',
			'1EC4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQxlCHRoCkMRYHUSA4gGNyGKiQDHWBoHWABS9IDGGKQFI7luZNTVs6apVUVFI7oOoA5qIoZcxNARDTKABXR1IJ4pbQjDdPFDhR0WIxX0A5NvKaYcvCCgAAAAASUVORK5CYII=',
			'E5A3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMIQ6IIkFNIg0MIQyOgSgiTE6OoBkkMVCWIFkAJL7QqOmLl26KmppFpL7gPKNrgh1CLHQAHTzwOpQxVhbWRsCUdwSGsIIshfFzQMVflSEWNwHAGAkzwTlzSZnAAAAAElFTkSuQmCC',
			'D5E6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHaY6IIkFTBFpYG1gCAhAFmsFiTE6CKCKhYDEkN0XtXTq0qWhK1OzkNwX0MrQ6NrAiGYeWMxBBNU8TLEprK3obgkNYAxBd/NAhR8VIRb3AQAh8sz/pKg4+QAAAABJRU5ErkJggg==',
			'9B47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQxgaHUNDkMREpoi0MrQ6NIggiQW0ijQ6TMUQa2UIdGgIQHLftKlTw1ZmZq3MQnIfq6tIK2ujQyuKzUDzXEMDpiCLCYDsaHQIYEB3S6OjAxY3o4gNVPhREWJxHwBe2szZNZxXlAAAAABJRU5ErkJggg==',
			'49C5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37pjCGMIQ6hgYgi4WwtjI6BDogq2MMEWl0bRBEEWOdAhJjdHVAct+0aUuXpq5aGRWF5L6AKYyBrkBaBElvaChDI7oYwxQWsB2oYiC3BASguA/sZoepDoMh/KgHsbgPAPL9y3KfOqFWAAAAAElFTkSuQmCC',
			'2F7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANEQ11DA0MDkMREpogAyUAHZHUBrZhiDCCxRkeYGMRN06aGrVq6MjQL2X0BQHVTGFH0MjoAxQJQxVgbRIDiqGIiQMjagCoWGgoWQ3HzQIUfFSEW9wEAGITJb2ImV+kAAAAASUVORK5CYII=',
			'DD16' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgNEQximMEx1QBILmCLSyhDCEBCALNYq0ugYwugggCbmMIXRAdl9UUunrcyatjI1C8l9UHUY5oH0ihASA7llCqpbQG5mDHVAcfNAhR8VIRb3AQAyU83n8mw67gAAAABJRU5ErkJggg==',
			'0DF9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA6Y6IImxBoi0sjYwBAQgiYlMEWl0BaoWQRILaEURAzspaum0lamhq6LCkNwHUccwFVMv0FwMOxhQ7MDmFrCbgeYhu3mgwo+KEIv7AJd5y7w15rXqAAAAAElFTkSuQmCC',
			'4872' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QwQ2AIAxFy6EbMFDdoCbgwWnKgQ2QDTzIlMLJEjxqQv/t5bd5KZRhBGbKP37JOPR8kGYOIwgzK2acDSQrWcUw1V6lVvnlfG3lLGVXftx6qTWfXe/rPYbYu9iwUG12DCMK8OAsxrsZ/vddXvxujZjMUOOTj78AAAAASUVORK5CYII=',
			'A00F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YAhimMIaGIImxBjCGMIQCZZDERKawtjI6OqKIBbSKNLo2BMLEwE6KWjptZeqqyNAsJPehqQPD0FBMsYBWbHZguiWgFexmFLGBCj8qQizuAwDKRsmlCa3aGQAAAABJRU5ErkJggg==',
			'771A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMLSiiLYyNDqEMEx1QBNzDGEICEAWA+mbwuggguy+qFXTVk1bmTUNyX2MDgwBSOrAkBUkOoUxNARJTAQoiq4uACiKTYwx1BFFbKDCj4oQi/sAdSzKrehuKVkAAAAASUVORK5CYII=',
			'B2C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhCHVqRxQKmsLYyOgRMRRFrFWl0bRAIRVXHABRjgOkFOyk0atXSpatWLUV2H1DdFFaEOqh5DAGYYowOrA0C6G5pALoFRSw0QDTUAQgDBkH4URFicR8AtobNZYuB8xcAAAAASUVORK5CYII=',
			'74FE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkMZWllDA0MDkEVbGaayNjA6MKCKhWKITWF0RRKDuClq6dKloStDs5Dcx+gg0oqul7VBNNQVTUwEaAu6ugDcYqhuHqDwoyLE4j4AqbDIqdIkr8AAAAAASUVORK5CYII=',
			'CC79' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDA6Y6IImJtLI2OjQEBAQgiQU0ijQ4NAQ6iCCLNQB5jY4wMbCTolZNW7Vq6aqoMCT3gdVNYZiKoTeAoUEEzQ5HBwYUO0BucQWqRHYL2M0NDChuHqjwoyLE4j4ApwzNKRtT5JMAAAAASUVORK5CYII=',
			'26E3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHUIdkMREprC2sjYwOgQgiQW0ijSyguSQdbeKNIDEApDdN21a2NLQVUuzkN0XINqKpA4MGR1EGl3RzGNtwBQD2oDhltBQTDcPVPhREWJxHwA+kct/wD28KQAAAABJRU5ErkJggg==',
			'C760' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QsRGAMAhFScEGuA8W9niXNI7gFKTICMkGFmZKUxK11FPo3vHhHVAvpfCnfsUP/RA4QLKMEsRx5MyGSYQ4KYtYppBQHZPxW2otW97XYvzanGBbSF3WMercs4iKKt0NSqTu5IK+pU7OX/3vwb7xOwChf8xyze91XQAAAABJRU5ErkJggg==',
			'5A52' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAfElEQVR4nM2QsQ2EQAwEfYE7MP2YgHxfOhOQE/xX4QuuA6AHqJIL/YLwX8KbjVbakem4nNOT8hc/ywQ2XTUweMrsBHwxruxJJbAXpAwruQS/cdv2+f05puhXpaijxA2qnTVWowtab3Askckipe8VkXHbVUuWH/C/H+bG7wRW8s02a670WgAAAABJRU5ErkJggg==',
			'5224' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM3QsQ2AIBCF4aNgA9wHC/tnwlkwglMcBRsQN7CQKaU8oqVGue7LEf5A9XKE/jSv9HEwgZgEyiA2m9Gn3lyaBFnbDEpeUKD6lq3u9Vhj1H2ZCmXj9d1moGI46DfaTtOuxRUrTTuzGHhidPbV/z04N30n8ZjNW+ndZ5UAAAAASUVORK5CYII=',
			'716A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGVpRRFsZAxgdHaY6oIixBrA2OAQEIItNYQCKMTqIILsvalXU0qkrs6YhuY/RAajO0RGmDgxZG0B6A0NDkMREIGIo6oD2Ad3iiCbGGsoQyogiNlDhR0WIxX0A/4HIzSK9/H0AAAAASUVORK5CYII=',
			'18C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHaY6IImxOrC2MjoEBAQgiYk6iDS6Ngg6iKDoZW1lBdIiSO5bmbUybCmQjkJyH1RdowOKXpB5DK0MGGICUxjQ7AC5BVlMNATkZsfQkEEQflSEWNwHALjayWuw81xNAAAAAElFTkSuQmCC',
			'363F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RAMYQxhDGUNDkMQCprC2sjY6OqCobBVpZGgIRBWbItLAgFAHdtLKqGlhq6auDM1Cdt8U0VYGLOY5oJuHRQybW6BuRtU7QOFHRYjFfQDC1soxFZA+5AAAAABJRU5ErkJggg==',
			'E7DC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNEQ11DGaYGIIkB2Y2ujQ4BIuhiDYEOLKhiraxAMWT3hUatmrZ0VWQWsvuA6gKQ1EHFGB0wxVgbWDHsEGlgRXNLaAhQDM3NAxV+VIRY3AcAmMbNLy5/xggAAAAASUVORK5CYII=',
			'98E0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHVqRxUSmsLayNjBMdUASC2gVaXRtYAgIQBEDqWN0EEFy37SpK8OWhq7MmobkPlZXFHUQCDYPVUwAix3Y3ILNzQMVflSEWNwHAA0IyzsTE/X6AAAAAElFTkSuQmCC',
			'79C5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHUMDkEVbWVsZHQIdUFS2ijS6Ngiiik0BiTG6OiC7L2rp0tRVK6OikNzH6MAY6AqkRZD0sjYwNKKLiTSwgO1AFgtoALklICAARQzkZoepDoMg/KgIsbgPAO1zy2z3goVfAAAAAElFTkSuQmCC',
			'CDB0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WENEQ1hDGVqRxURaRVpZGx2mOiCJBTSKNLo2BAQEIIs1AMUaHR1EkNwXtWraytTQlVnTkNyHpg4h1hCIKobFDmxuwebmgQo/KkIs7gMAz+DOUgQQV/0AAAAASUVORK5CYII=',
			'1F2A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGVqRxVgdRBoYHR2mOiCJiQLFWBsCAgJQ9IoAyUAwCXPfyqypYatWZmZNQ3IfWF0rI0wdQmwKY2gIulgApjpGB1Qx0RCgW0IDUcQGKvyoCLG4DwAtS8f1qOnkvAAAAABJRU5ErkJggg==',
			'0414' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YWhmmMDQEIImxBjBMZQhhaEQWE5nCEMoYwtCKLBbQyugK1DslAMl9UUuXLl01bVVUFJL7AlpFgHYwOqDqFQ11mMIYGoJqBza3YIiB3MwY6oAiNlDhR0WIxX0AS7PMcEynz2wAAAAASUVORK5CYII=',
			'9ACB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHUMdkMREpjCGMDoEOgQgiQW0srayNgg6iKCIiTS6NjDC1IGdNG3qtJWpq1aGZiG5j9UVRR0EtoqGgsSQzRMAm4dqh8gUkUZHNLewBog0OqC5eaDCj4oQi/sASJXLqhBnxC8AAAAASUVORK5CYII='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>
