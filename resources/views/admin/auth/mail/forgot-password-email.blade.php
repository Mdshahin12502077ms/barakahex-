<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,600" rel="stylesheet" type="text/css">
    <!-- Web Font / @font-face : BEGIN -->
    <!--[if mso]>
        <style>
            * {
                font-family: 'Roboto', sans-serif !important;
            }
        </style>
    <![endif]-->

    <!--[if !mso]>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,600" rel="stylesheet" type="text/css">
    <![endif]-->

    <!-- Web Font / @font-face : END -->

    <!-- CSS Reset : BEGIN -->


    <style>
        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            font-family: 'Roboto', sans-serif !important;
            font-size: 14px;
            margin-bottom: 10px;
            line-height: 24px;
            /* color:#8094ae; */
            color: #939393;
            font-weight: 400;
        }
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
        }
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        table table table {
            table-layout: auto;
        }
        a {
            text-decoration: none;
        }
        img {
            -ms-interpolation-mode:bicubic;
        }


        /* New */
        .logo_area {
            border: 1px solid #e7e5e5;
            border-bottom: none;
            border-radius: 20px 20px 0px 0px;
            border-collapse: initial !important;
        }
        .content_area {
            border: 1px solid #e7e5e5;
            border-top: none;
            border-radius: 0px 0px 20px 20px;
            border-collapse: initial !important;
        }
        .logo_area td {
            border-bottom: 1px solid #e7e5e5;
        }
    </style>

</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f5f6fa;">
    <center style="width: 100%; background-color: #f5f6fa;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f5f6fa">
            <tr>
               <td style="padding: 40px 0;">
                    <table class="logo_area" style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding: 50px 0 25px 0">
                                    <a href="{{ route('home') }}"><img style="height: 40px" src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : getFileLink('80X80', []) }}" alt="logo"></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="content_area" style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="text-align:center;padding: 25px 80px 15px 80px;">
                                    <span style="font-size: 16px; margin-bottom: 8px; color:#575757; font-weight:600; display: block;">Hi {{$user->first_name .' '.$user->last_name}},</span>
                                    <p style="margin-bottom: 20px;">{!! $body !!}</p>
                                    <a href="{{ $reset_link }}" style="background-color:#4c83ee;border-radius:6px;color:#ffffff;display:inline-block;font-size:15px;font-weight:500;text-align:center;text-decoration:none;text-transform: capitalize; padding: 10px 30px;">{{__('reset_password')}}</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:center;padding: 10px 80px 60px">
                                    <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb;">{{__('reset_password_message')}}.{{ setting('email_address') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
               </td>
            </tr>
        </table>
    </center>
</body>
</html>
