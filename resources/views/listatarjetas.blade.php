<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tarjetas</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .card-list { list-style: none; padding: 0; }
        .card-item { background: #f4f4f4; padding: 10px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>Mis Tarjetas</h2>
    <ul id="card-list" class="card-list"></ul>

    <script>
        async function fetchCards() {
            try {
                const response = await fetch("http://127.0.0.1:8000/api/list-cards", {
                    method: "GET",
                    headers: {
                        "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTc0MTQ2MTYyNCwiZXhwIjoxNzQxNDY1MjI0LCJuYmYiOjE3NDE0NjE2MjQsImp0aSI6InducE9ueDZwQnkwcExkNksiLCJzdWIiOjEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.05zxnrY8m4Y5QuYCQr5QPW5AJg9wyavUJoGCx7h_Ffo", // Reemplaza con tu token JWT
                        "Content-Type": "application/json"
                    }
                });

                const data = await response.json();

                if (!data.success) {
                    console.error("Error al obtener tarjetas:", data.message);
                    return;
                }

                const cardList = document.getElementById("card-list");
                cardList.innerHTML = "";

                data.cards.forEach(card => {
                    const li = document.createElement("li");
                    li.classList.add("card-item");
                    li.innerHTML = `
                        <strong>${card.brand.toUpperCase()}</strong> •••• ${card.last4} <br>
                        Expira: ${card.exp_month}/${card.exp_year}
                    `;
                    cardList.appendChild(li);
                });

            } catch (error) {
                console.error("Error en la petición:", error);
            }
        }

        fetchCards();
    </script>
</body>
</html>
