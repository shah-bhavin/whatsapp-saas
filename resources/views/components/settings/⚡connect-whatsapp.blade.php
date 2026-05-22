<?php

use Livewire\Component;

new class extends Component
{
    
};
?>

<div class="p-6">

    <h1 class="text-2xl font-bold mb-5">
        Connect WhatsApp
    </h1>

    <button onclick="launchWhatsAppSignup()" style="background-color: #1877f2; border: 0; border-radius: 4px; color: #fff; cursor: pointer; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; height: 40px; padding: 0 24px;">Login with Facebook</button>
    
    <script>
    
    const launchWhatsAppSignup = () => {
        // Launch Facebook login
        FB.login(fbLoginCallback, {
            config_id: '853378571145401', // configuration ID goes here
            response_type: 'code', // must be set to 'code' for System User access token
            override_default_response_type: true, // when true, any response types passed in the "response_type" will take precedence over the default types
            extras: {"version":"v4","setup":{"business":{"id":null,"name":null,"email":null,"phone":{"code":null,"number":null},"website":null,"address":{"streetAddress1":null,"streetAddress2":null,"city":null,"state":null,"zipPostal":null,"country":null},"timezone":null},"phone":{"displayName":null,"category":null,"description":null},"preVerifiedPhone":{"ids":null},"solutionID":null,"whatsAppBusinessAccount":{"ids":null}}}
        });
    }

    // REMOVED 'async' keyword from this line to fix the error
    const fbLoginCallback = (response) => {
        if (response.authResponse) {
            const code = response.authResponse.code;

            // SECURITY WARNING: Your client_secret is exposed on the frontend!
            // This must be moved to your backend server immediately to protect your app.
            const url = "https://graph.facebook.com/v25.0/oauth/access_token";
            
            fetch('/meta/exchange-token', {

                method: 'POST',

                headers: {

                    'Content-Type': 'application/json',

                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}'
                },

                body: JSON.stringify({

                    code: code
                })
            })
            .then(res => res.json())

            .then(data => {

                console.log(data);
            })
            .catch(error => {
                console.error("API Error:", error);
            });
        }
    }


    </script>

    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '{{ env("FACEBOOK_CLIENT_ID") }}',
                autoLogAppEvents : true,
                xfbml: true,
                version: 'v25.0'
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) { return; }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        


    </script>

    <script>
    window.addEventListener('message', function(event) {
        // Security
        if (event.origin !== "https://www.facebook.com") {
            return;
        }

        try {
            const data = JSON.parse(event.data);
            console.log('EMBEDDED SIGNUP DATA', data);

            // Only Finish Event
            if (data.type === 'WA_EMBEDDED_SIGNUP' && data.event === 'FINISH') {
                // Send To Laravel
                fetch('/meta/save-account', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        waba_id: data.data.waba_id,
                        phone_number_id: data.data.phone_number_id
                    })
                })
                .then(res => res.json())
                .then(result => {
                    console.log(result);
                    if (result.success) {
                        alert('WhatsApp Connected Successfully');
                    } else {
                        alert(result.message);
                    }
                });
            }
        } catch (e) {
            console.log('Non JSON event');
        }
    });
    </script>

</div>