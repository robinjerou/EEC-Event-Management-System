document.addEventListener('DOMContentLoaded', function() {
    // Function to fetch user info and update header
    function fetchUserInfo() {
        // Make AJAX request
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'assets/php/get-user-info.php', true);
        xhr.onload = function() {

            if (xhr.status >= 200 && xhr.status < 400) {

                try {
                    var data = JSON.parse(xhr.responseText);
                    console.log('JSON Response:', data);
                    console.log(data.firstName);
                    console.log(data.lastName);
                    if (data.hasOwnProperty('firstName') && data.hasOwnProperty('lastName')) {
                        document.querySelector('.logo-text').textContent = 'Welcome, ' + data.firstName + ' ' + data.lastName;
                    } else {
                        console.error('Error: Unexpected response format');
                    }

                } catch (error) {
                    console.error('Error parsing JSON:', error);
                }

            } else {
                console.error('Request failed with status:', xhr.status);
            }

        };

        xhr.onerror = function() {
            console.error('Request failed');
        };

        xhr.send();
    }
    fetchUserInfo();
});
