<?php
$sumPrice=0;
$message.='<p style="color:#363636;margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;width: 70%;text-align: justify;font-size: 1rem">
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<p style="margin: 0 auto;padding: 0;color:#363636;font-family: &quot;Calibri&quot;;width: 70%;text-align: justify;font-size: 1rem">
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><br>';

foreach ($popis as $id => $stuff) {

$message.='
                    <p style="color:#363636;display: flex;margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;width: 70%; justify-content: space-between;"><span style="margin: 0;text-align: left">'.$stuff.'</span><span style="display:flex;text-align: right;margin: 0;margin-left:auto">'.$cena[$id].'</span>
                     </p>
                    <br style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;">
                    ';
}
$message.='

	
	<br><p style="text-align: center; margin: 0 auto"><a class="link" style="border-radius: 5px;padding: 8px 14px;color:white;background: #4e4e4e;font-family:&quot;Century Gothic&quot;;text-decoration:none" href="http://cron.feris.sk/upload/'.$link.'">Nahrať súbory</a></p><br>
'
?>