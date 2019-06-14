<?php  $message.='<div class="flex w-70 sign" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;display: flex;justify-content: center;align-items: center;width: 70%;padding-top: 20px;">
                        <div class="m-l-0 w-50" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;margin-left: 0;width: 50%;">
                            <p class="m-l-0" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;margin-left: 0;width: 70%;text-align: justify;">S pozdravom</p>
                            <p class="m-l-0" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;margin-left: 0;width: 70%;text-align: justify;">Pavol Ladňák</p>
                        </div>
                        <img src="http://cron.feris.sk/public/img/mail/podpis.png" class="podpis" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;margin-right: 2em;height: 60px;object-fit: cover;">
                    </div>

                </div>
            </div>
            <div class="footer flex" style="margin: 0 auto;padding: 30px;font-family: &quot;Calibri&quot;;width: 70%;display: flex;justify-content: center;align-items: center;">
                <div class="w-50 text-center" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;text-align: center;width: 50%;">
                    <img src="http://cron.feris.sk/public/img/mail/logo_black.png" alt="" class="logo" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;width: 70%;">
                </div>
                    <div style="">
                <div class="w-50 flex a-i-start" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;display: flex;justify-content: center;align-items: flex-start;width: 50%;">
                    <div style="display: flex;flex-wrap: wrap;margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;">
                        <span class="imp col-4 " style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;font-weight: bold;max-width: 20.333333%;">IČO: </span><span style="margin: 0 auto;padding: 0;color:#717171;font-family: &quot;Calibri&quot;;" class="col-8">2024135850</span><br style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;">
                        <span class="imp col-4" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;font-weight: bold;max-width: 20.333333%;">DIĆ: </span><span style="margin: 0 auto;padding: 0;color:#717171;font-family: &quot;Calibri&quot;;" class="col-8">2024135850</span>
                    </div>
                    <div style="display: flex;flex-wrap: wrap;margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;width:17rem">
                        <span class="imp col-4" style="max-width: 33.333333%;margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;font-weight: bold;">Telefón: </span><span class="col-8" style="margin: 0 auto;padding: 0;color:#717171;font-family: &quot;Calibri&quot;;">+421 123 456</span><br style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;">
                        <span class="imp col-4" style="max-width: 33.333333%;margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;font-weight: bold;">Email: </span><span style="margin: 0 auto;padding: 0;color:#717171;font-family: &quot;Calibri&quot;;" class="col-8">probim@probim.sk</span><br style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;">
                        <span class="imp col-4" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;font-weight: bold;max-width: 33.333333%;">Adresa: </span><span style="margin: 0 auto;padding: 0;color:#717171;font-family: &quot;Calibri&quot;;" class="col-8">Priekopská 53,
        <br style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;">036 08 Martin-Priekopa</span><br style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;">
                    </div>
                </div>
            </div>
            </div>
            <div class="text-center footer_down" style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;text-align: center;width: 70%;">
                <hr style="margin: 0 auto;padding: 0;font-family: &quot;Calibri&quot;;">
                <p style="color:#717171;margin: 0 auto;padding: 10px 0;font-family: &quot;Calibri&quot;;">Všetky práva vyhradené © 2019</p>
            </div>
        </div>
    </body>
</html>
';

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
// More headers

$headers .= 'From: <'.$myMail.'>' . "\r\n";

?>
