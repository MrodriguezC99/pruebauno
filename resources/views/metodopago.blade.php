<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago con Stripe</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h2>Agregar Método de Pago</h2>

    <form id="payment-form">
        <div id="card-element"></div>
        <button type="submit">Agregar Tarjeta</button>
    </form>

    <script>
        const stripe = Stripe("pk_test_51R03fUH5L1svepCqyIU7PzxGUOreXJdOkmpbrhdUB5U39AB537ublwVhkC75Q0p9egvPzs4sdC23CeZNJWTtl80T00kunMj48x"); // Reemplaza con tu clave pública
        const elements = stripe.elements();
        const cardElement = elements.create("card");
        cardElement.mount("#card-element");

        document.getElementById("payment-form").addEventListener("submit", async (event) => {
            event.preventDefault();

            // Crear un PaymentMethod en Stripe
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: "card",
                card: cardElement,
            });

            if (error) {
                console.error("Error:", error);
            } else {
                console.log("Payment Method ID:", paymentMethod.id);

                // Enviar el PaymentMethod ID al Backend
                fetch("http://127.0.0.1:8000/api/create-payment-method", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTc0MTQ2MTYyNCwiZXhwIjoxNzQxNDY1MjI0LCJuYmYiOjE3NDE0NjE2MjQsImp0aSI6InducE9ueDZwQnkwcExkNksiLCJzdWIiOjEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.05zxnrY8m4Y5QuYCQr5QPW5AJg9wyavUJoGCx7h_Ffo", // Reemplázalo si usas autenticación
                    },
                    body: JSON.stringify({
                        payment_method: paymentMethod.id,
                    }),
                })
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(error => console.error("Error:", error));
            }
        });
    </script>
</body>
</html>
