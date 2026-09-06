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
            color:#8094ae;
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

        /* New Css */
        .mail__body {
            padding: 0 30px;
            border-radius: 10px 10px 0 0;
        }
        .mail__body h2 {
            text-transform: capitalize;
        }
        .mail__body span {
            color: #6b6969;
            margin-right: 1px;
            text-transform: capitalize;
        }

        .mail__body p {
            font-size: 15px;
            margin: 5px 0;
            color: #a0a0a0;
            padding: 7px 15px;
            border: 1px solid #eeeeee;
            margin-bottom: 9px;
            margin: 5px 30px;
            border-radius: 5px;
        }

        @media (max-width: 479px) {
            .mail__body p {
                margin: 5px 10px;
            }

            .mail__body td {
                padding: 30px 20px !important;
            }
        }
        .mail__footer {
            border-top: 1px solid #eee;
            border-radius: 0 0 10px 10px ;
        }
        .mail__footer p {
            font-size: 15px;
            color: #a0a0a0;
        }
    </style>

</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f5f6fa;">
	<center style="width: 100%; background-color: #f5f6fa;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f5f6fa">
            <tr>
               <td style="padding: 40px 0;">
                    <table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff; border-radius: 10px 10px 0 0; ">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding: 40px 0 0px">
                                    <a href="#"><img style="height: 60px" src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : getFileLink('80X80', []) }}" alt="logo" class="logo"></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="mail__body" style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="padding: 20px 40px 40px; text-align: start;">
                                    <h2 style="text-align: center; font-size: 22px; color: #1ee0ac; font-weight: 500; margin-bottom: 30px;">{{__('you_have_got_contact')}}</h2>
                                    <p><span>name:</span> {{ $body['name'] }}</p>
                                    <p><span>email:</span> {{ $body['email'] }}</p>
                                    <p><span>phone:</span> {{ $body['phone'] }}</p>
                                    <p><span>message:</span> {{ $body['message'] }}</p>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="mail__footer" style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding:10px 20px;">
                                    <p style="font-size: 15px;">{{ __('copy_right') }} © <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                    {{ setting('copyright_title')}}</p>
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
