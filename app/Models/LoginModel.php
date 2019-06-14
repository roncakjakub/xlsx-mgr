<?php 
class LoginModel{
    private $session;
    public function __construct($session){
        $this->session=$session;
    }

	public function login($sess,$brutefile, $insertedPass, $settedMail, $settedPass, $sessName,$index) {
            if(password_verify($insertedPass, $settedPass)){
                // Password is correct!
                // Get the user-agent string of the user.
                $user_browser = $_SERVER['HTTP_USER_AGENT'];
                // XSS protection as we might print this value
                $sess->set('fake_login_string', hash('sha512', 
                          $settedPass . $user_browser));
                $sess->set('accI', $index);

                // Login successful.
                return true;
            } 
        $dom = new DOMDocument();
        $dom->formatOutput = true;

        $dom->load($brutefile, LIBXML_NOBLANKS);

        $root = $dom->documentElement;
        $newresult = $root->appendChild( $dom->createElement('group') );

        $newresult->appendChild( $dom->createElement('id',$index));
        $newresult->appendChild( $dom->createElement('IP',$_SERVER['REMOTE_ADDR']) );
        $newresult->appendChild( $dom->createElement('time',date("Y-m-d H:i:s")) );

        $dom->saveXML();
        $dom->save($brutefile) or die('XML Manipulate Error');
        return false;
    }
    function createCheckString() {
    $length = 10;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
    public function checkbrute($groups,$mins, $n,$id) {

        $checkArr=array();
        $controlTime= time() - ($mins*60);
        foreach ($groups as $group)
            if (strtotime($group->time)>$controlTime&&$id==$group->id) {
                
                if(isset($checkArr[(string)$group->IP]))
                $checkArr[(string)$group->IP]+=1;
                else
                $checkArr[(string)$group->IP]=1;
            if(in_array($n, $checkArr)) return $group->IP;
            }
        return 0;
    }

function login_check($lc,$sess,$settedPass,$locked) {
        $lc->setAccData($sess->accI);

    // Check if all session variables are set
    if($locked==0){ 
        if (isset($sess->login_string)){
            $login_string = $sess->login_string;
            // Get the user-agent string of the user.
            $user_browser = $_SERVER['HTTP_USER_AGENT'];
                
            $login_check = hash('sha512', $settedPass . $user_browser);
            if (hash_equals($login_check, $login_string) ){
                // Logged In!!!! 
                return true;
            } else return false;
        } else return false;
    }else return false;
}

function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}
}