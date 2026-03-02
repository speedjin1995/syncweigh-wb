// Listens for pendrive status messages from a local WebSocket server.
// The Python script on the PC should open a WebSocket on the chosen port
// (default 8765 below) and push a JSON message whenever the state changes.
// Example message format:
//    { "type": "pendrive", "connected": true }
// or { "type": "pendrive", "connected": false }
// On disconnect the page will automatically reload; when connected the
// elements marked with the `deduction-field` class are shown.

(function () {
    const WS_URL = "ws://localhost:5002/ws_license"; // adjust port if your Python server uses a different one
    let socket;

    // helper to show/hide deduction elements and sidebar
    function setPendriveState(connected) {
        if (connected) {
            document.body.classList.remove("no-pendrive");
            document.body.classList.add("has-pendrive");
            document.querySelectorAll(".deduction-field").forEach(el => el.style.display = "");
        } else {
            document.body.classList.remove("has-pendrive");
            document.body.classList.add("no-pendrive");
            document.querySelectorAll(".deduction-field").forEach(el => el.style.display = "none");
        }
    }

    // start with hidden state so there is no flicker when the page loads
    document.addEventListener('DOMContentLoaded', function () {
        setPendriveState(false);
    });

    function connect() {
        socket = new WebSocket(WS_URL);

        socket.onopen = function () {
            console.log("pendrive ws open");
            // optionally ask for current state if your server supports it
            // socket.send(JSON.stringify({ type: 'status_request' }));
        };

        socket.onmessage = function (evt) {
            let msg;
            try {
                msg = JSON.parse(evt.data);
            } catch (e) {
                console.error("invalid message", evt.data);
                return;
            }

            if (msg.type === "pendrive") {
                // toggle entire UI using helper
                setPendriveState(msg.connected);
                // no automatic reload any more; if your server must be aware you
                // could trigger a silent AJAX call here instead
            }
        };

        socket.onclose = function () {
            console.warn("pendrive ws closed, retrying in 5s");
            setTimeout(connect, 5000);
        };

        socket.onerror = function (err) {
            console.error("pendrive ws error", err);
        };
    }

    connect();
})();
