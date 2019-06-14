<?php
$to = "ladnak.erik@gmail.com";
$subject = "HTML email";

$message = '
<html style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
    <head style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
        <meta charset="utf-8" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
        <style media="screen" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
        </style><style media="screen" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
            .text-center{
                text-align: center;
            }
            *{
                margin: 0 auto;
                padding: 0;
                font-family: "Roboto";
            }
            .wrapper{
                width: 100%;
                background-color: #e6e6e6;
                padding: 30px;
                box-sizing: border-box;
            }
            .upper,.footer,.footer_down{
                width: 70%;
                    margin: 0 auto;
            }
            .upper{
                box-shadow: 0px 0px 30px -9px rgba(0,0,0,0.75);
            }
            .footer_down p{
                padding: 10px 0;
            }
            .footer{
                padding: 30px;
            }
            .social{
                color: white;
                font-size: 1.5em;
                margin-right: 10px;
            }
            .flex{
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .m-r-0{
                margin-right: 0;
            }
            .m-l-auto
            {
                margin-left: auto;

            }
            .w-70{
                width: 70%;
            }
            .m-l-0{
                margin-left: 0;
            }
            .banner hr{
                margin: 10px auto;
            }
            .header{
                background-color: #252525;
                padding:10px 30px;
            }
            .banner{
                padding: 30px;
                background-color: #363636;
                text-align: center;
                color: white;
            }
            .banner h1{
                color: #f5981e;
                margin: 5px 0;
            }
            .hr_my{
                width: 30%;
            }
            .text{
                background-color: white;
                padding: 50px 0;
            }
            .text p
            {
                width: 70%;
            }
            .podpis{
                width: 20%;
                height: 50px;
                object-fit: cover;
            }
            .text p {
                text-align: justify;
            }
            .text-left{
                text-align: left;
            }
            .logo{
                width: 70%;
            }.imp{
                font-weight: bold;
            }
            .w-25{
                width: 25%;
            }
            .w-50{
                width: 50%;
            }
            .w-100{
                width: 100%;
            }
            .a-i-start{
                align-items: flex-start;
            }
            .flex-d-c{
                flex-direction: column;
            }
            .link{
                color: #f5981e;
                text-decoration: none;
            }
            .logo_upper{
                width: 30%;
            }
            .sign{
                padding-top: 20px;
            }
            @media only screen and (max-width: 1000px) {
                .wrapper{
                    padding:0px;
                    overflow-x: hidden;
                }
                .upper{
                    width:100%;
                }
                .podpis{
                    width: 25%!important;
                }
            }
            @media only screen and (max-width: 700px) {
                .text p {
                    width: 90%;
                }
                .w-70{
                    width: 90%;
                }
            }
            @media only screen and (max-width: 600px) {
                .logo_upper {
                    width: 50%;
                }
                .footer{
                    width: 90%;
                    flex-direction: column;
                }
                .w-50{
                    width: 90%;
                }
                .upper{
                    box-shadow: none;
                }
                .logo{
                    width: 50%;
                    margin-bottom: 10px;
                    text-align: center;
                }
                .text-center{
                    text-align: center;
                }

            }
        </style>
        
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
    </head>
    <body style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
        <div class="wrapper" style="margin: 0 auto;padding: 30px;font-family: &quot;Roboto&quot;;width: 100%;background-color: #e6e6e6;box-sizing: border-box;">
            <div class="upper" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;width: 70%;box-shadow: 0px 0px 30px -9px rgba(0,0,0,0.75);">
                <div class="header flex" style="margin: 0 auto;padding: 10px 30px;font-family: &quot;Roboto&quot;;display: flex;justify-content: center;align-items: center;background-color: #252525;">
                    <img src="http://cron.feris.sk/mail/img/logoPROBIM.png" alt="" class="m-l-0 logo_upper" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;margin-left: 0;width: 30%;">
                    <div class="m-r-0 m-l-auto" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;margin-right: 0;margin-left: auto;">
                        <a href="#" class="social" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;color: white;font-size: 1.5em;margin-right: 10px;"><i class="fab fa-instagram" style="margin: 0 auto;padding: 0;font-family: &quot;Font Awesome 5 Brands&quot;;-moz-osx-font-smoothing: grayscale;-webkit-font-smoothing: antialiased;display: inline-block;font-style: normal;font-variant: normal;text-rendering: auto;line-height: 1;"></i> <img src="http://cron.feris.sk/mail/img/google_plus.svg"></a>
                        <a href="#" class="social" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;color: white;font-size: 1.5em;margin-right: 10px;"><i class="fab fa-facebook" style="margin: 0 auto;padding: 0;font-family: &quot;Font Awesome 5 Brands&quot;;-moz-osx-font-smoothing: grayscale;-webkit-font-smoothing: antialiased;display: inline-block;font-style: normal;font-variant: normal;text-rendering: auto;line-height: 1;"></i><img src="http://cron.feris.sk/mail/img/facebook.svg"></a>
                    </div>
                </div>
                <div class="banner" style="margin: 0 auto;padding: 30px;font-family: &quot;Roboto&quot;;background-color: #363636;text-align: center;color: white;">
                    <h1 style="margin: 5px 0;padding: 0;font-family: &quot;Roboto&quot;;color: #f5981e;">Pripomenutie 1.</h1>
                    <hr class="hr_my" style="margin: 10px auto;padding: 0;font-family: &quot;Roboto&quot;;width: 30%;">
                    <p style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eius</p>
                    <p style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">Lorem ipsum dolor sit amet</p>
                </div>
                <div class="text" style="margin: 0 auto;padding: 50px 0;font-family: &quot;Roboto&quot;;background-color: white;">
                    <p style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;width: 70%;text-align: justify;">Dobtjdkf sd</p>
                    <br style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                    <p style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;width: 70%;text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. <a href="#" class="link" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;color: #f5981e;text-decoration: none;"> fasdfasd</a></p>
                    <br style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                    <p style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;width: 70%;text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia <a href="#" class="link" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;color: #f5981e;text-decoration: none;"> fasdfasd</a> deserunt mollit anim id est laborum.</p>
                    <br style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                    <p style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;width: 70%;text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud <a href="#" class="link" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;color: #f5981e;text-decoration: none;"> fasdfasd</a> exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    <br style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                    <div class="flex w-70 sign" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;display: flex;justify-content: center;align-items: center;width: 70%;padding-top: 20px;">
                        <div class="m-l-0 w-50" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;margin-left: 0;width: 50%;">
                            <p class="m-l-0" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;margin-left: 0;width: 70%;text-align: justify;">adjlf ds</p>
                            <p class="m-l-0" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;margin-left: 0;width: 70%;text-align: justify;">n lasdafsd</p>
                        </div>
                        <img src="http://cron.feris.sk/mail/img/podpis.png" class="podpis m-r-0 w-50" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;margin-right: 0;width: 50%;height: 50px;object-fit: cover;">
                    </div>

                </div>
            </div>
            <div class="footer flex" style="margin: 0 auto;padding: 30px;font-family: &quot;Roboto&quot;;width: 70%;display: flex;justify-content: center;align-items: center;">
                <div class="w-50 text-center" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;text-align: center;width: 50%;">
                    <img src="http://cron.feris.sk/mail/img/logoPROBIM.png" alt="" class="logo" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;width: 70%;">
                </div>
                <div class="w-50 flex a-i-start" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;display: flex;justify-content: center;align-items: flex-start;width: 50%;">
                    <div style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                        <span class="imp" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;font-weight: bold;">ICon: </span><span style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">sf sdlkf</span><br style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                        <span class="imp" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;font-weight: bold;">ICon: </span><span style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">sf sdlkf</span>
                    </div>
                    <div style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                        <span class="imp" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;font-weight: bold;">ICon: </span><span style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">sf sdlkf</span><br style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                        <span class="imp" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;font-weight: bold;">ICon: </span><span style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">sf sdlkf</span><br style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                        <span class="imp" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;font-weight: bold;">ICon: </span><span style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">gs dfgsdf g <br style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">fsd fsad  </span><br style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                    </div>
                </div>
            </div>
            <div class="text-center footer_down" style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;text-align: center;width: 70%;">
                <hr style="margin: 0 auto;padding: 0;font-family: &quot;Roboto&quot;;">
                <p style="margin: 0 auto;padding: 10px 0;font-family: &quot;Roboto&quot;;"> jskdfgjdslghsdlf</p>
            </div>
        </div>
    </body>
</html>

';

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
// More headers
$headers .= 'From: <jakub.roncak@gmail.com>' . "\r\n";
$headers .= 'Cc: myboss@example.com' . "\r\n";

mail($to,$subject,$message,$headers);
?>
