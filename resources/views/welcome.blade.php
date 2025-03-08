<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago con Stripe</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            text-align: center;
        }
        #card-element {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        button:disabled {
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <h2>Ingresa los datos de tu tarjeta</h2>

    <div id="card-element"></div>

    <button id="submit-button">Guardar Tarjeta</button>

    <p id="message"></p>

    <script>
        const stripe = Stripe("pk_test_51R03fUH5L1svepCqyIU7PzxGUOreXJdOkmpbrhdUB5U39AB537ublwVhkC75Q0p9egvPzs4sdC23CeZNJWTtl80T00kunMj48x"); // Reemplaza con tu clave pública de Stripe
        const elements = stripe.elements();
        const cardElement = elements.create("card");

        cardElement.mount("#card-element");

        document.getElementById("submit-button").addEventListener("click", async () => {
            document.getElementById("submit-button").disabled = true;
            document.getElementById("message").textContent = "Procesando...";

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: "card",
                card: cardElement,
            });

            if (error) {
                document.getElementById("message").textContent = "Error: " + error.message;
                document.getElementById("submit-button").disabled = false;
            } else {
                console.log("Payment Method ID:", paymentMethod.id);
                sendPaymentMethodToBackend(paymentMethod.id);
            }
        });

        function sendPaymentMethodToBackend(paymentMethodId) {
            fetch("http://127.0.0.1:8000/api/payment-method", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    //"Authorization": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTc0MTQxMDgzOCwiZXhwIjoxNzQxNDE0NDM4LCJuYmYiOjE3NDE0MTA4MzgsImp0aSI6Ijh4emFQY3lIZVUzV2l5TjkiLCJzdWIiOjMsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.agHw95B3aWDFwJXGKylcq8bVcaVZ6-WIFvB5TyYUkgY", // Reemplaza con el token del usuario autenticado
                },
                body: JSON.stringify({ payment_method: paymentMethodId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "SUCCESS") {
                    document.getElementById("message").textContent = "Tarjeta guardada correctamente.";
                } else {
                    document.getElementById("message").textContent = "Error: " + data.message;
                }
                document.getElementById("submit-button").disabled = false;
            })
            .catch(error => {
                document.getElementById("message").textContent = "Error en la petición.";
                document.getElementById("submit-button").disabled = false;
            });
        }
    </script>
</body>
</html>
