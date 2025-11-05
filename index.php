<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Оплата заказа</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap');

        body {
            background: #fafbfc;
            margin: 0;
            padding: 0;
            font-family: "Playfair Display", serif;
        }

        h1, h2, p, input, label, button {
            font-family: "Playfair Display", serif;
        }

        h1 {
            text-align: center;
            color: #222;
            font-weight: 700;
            margin-top: 40px;
            letter-spacing: 1px;
        }

        form {
            background: #fff;
            max-width: 400px;
            margin: 20px auto 60px;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 24px 0 rgb(0 0 0 / 10%);
            position: relative;
        }

        /* Шаги сверху */
        #step-counter {
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            color: #FF0099;
            margin-bottom: 8px;
            user-select: none;
            font-style: italic;
        }

        label {
            display: block;
            margin-bottom: 14px;
            font-size: 17px;
        }

        select,
        input[type="date"],
        input[type="email"],
        input[type="text"],
        input[type="time"] {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #d6dbe2;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 5px;
            background: #f6f7fa;
            margin-bottom: 18px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

    
        input:focus {
            background: #fff;
            box-shadow: 0 0 8px 2px #ff0099aa;
        }

        button {
            padding: 13px 0;
            background: #FF0099;
            color: #fff;
            font-size: 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            width: 48%;
            margin-top: 20px;
        }

        button:hover {
            background: #000000ff;
        }

        button[disabled] {
            background: #ff009933;
            cursor: default;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
        }

     
        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

     
        #progress-container {
            width: 100%;
            background: #000000ff;
            height: 4px;
            border-radius: 8px;
            margin: 10px 0 10px 0;
            overflow: hidden;
        }

        /* Progress Bar fill */
        #progress-bar {
            height: 100%;
            width: 0%;
            background: #FF0099;
            transition: width 0.3s ease;
        }

        #result {
            position: fixed;
            bottom: 20px;
            right: 20px;
            max-width: 280px;
            padding: 12px 14px;
            background: #000000ff;
            border-radius: 8px;
            font-size: 14px;
            box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);
            opacity: 0;
            transform: translateX(100%) translateY(20px);
            pointer-events: none;
            transition: opacity 0.4s ease, transform 0.4s ease;
            z-index: 1000;
            color: #ffffffff;
        }

        #result.show {
            opacity: 1;
            transform: translateX(0) translateY(0);
            pointer-events: auto;
        }
    </style>
</head>
<body>
    <h1>Оплата заказа</h1>

    <form id="myForm" novalidate>
        <div id="step-counter">Шаг 1 из 3</div>

        <div id="progress-container">
            <div id="progress-bar"></div>
        </div>

        <div class="form-step active" data-step="1">
            <label for="fio">Как вас зовут?</label>
            <input type="text" id="fio" name="fio" placeholder="Фамилия Имя Отчество" required />

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="Ваш E-mail" required />

            <label for="phone">Мобильный телефон</label>
            <input type="text" id="phone" name="phone" placeholder="+7 (___) ___-__-__" required />
        </div>

        <div class="form-step" data-step="2">
            <label for="eventType">Выберите тип оплаты:</label>
            <select id="eventType" name="eventType" required>
                <option value="">Выберите...</option>
                <option value="Наличные">Наличные</option>
                <option value="Карта">Картой</option>
                <option value="Курьер">Курьером</option>
            </select>

            <label for="eventDate">Дата:</label>
            <input type="date" id="eventDate" name="eventDate" required />

            <label for="eventTime">Время:</label>
            <input type="time" id="eventTime" name="eventTime" required />
        </div>

        <!-- Step 3 -->
        <div class="form-step" data-step="3">
            <label for="address">Адрес доставки</label>
            <input type="text" id="address" name="address" placeholder="Введите адрес доставки" required />
        </div>

        <div class="buttons">
            <button type="button" id="prevBtn" disabled>Назад</button>
            <button type="button" id="nextBtn">Далее</button>
        </div>
    </form>

    <div id="result"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>

    <script>
        $(document).ready(function () {
            $('input[name="phone"]').mask("+7 (999) 999-99-99");
        });

        const form = document.getElementById("myForm");
        const steps = form.querySelectorAll(".form-step");
        const nextBtn = document.getElementById("nextBtn");
        const prevBtn = document.getElementById("prevBtn");
        const progressBar = document.getElementById("progress-bar");
        const stepCounter = document.getElementById("step-counter");
        const totalSteps = steps.length;
        let currentStep = 0;

        function showStep(index) {
            steps.forEach((step, i) => {
                step.classList.toggle("active", i === index);
            });
            prevBtn.disabled = index === 0;
            nextBtn.textContent = index === totalSteps - 1 ? "Оплатить" : "Далее";
            updateProgressBar(index);
            updateStepCounter(index);
        }

        function updateProgressBar(stepIndex) {
            // Показываем прогресс в %
            const progressPercent = ((stepIndex) / (totalSteps - 1)) * 100;
            progressBar.style.width = `${progressPercent}%`;
        }

        function updateStepCounter(stepIndex) {
            stepCounter.textContent = `Шаг ${stepIndex + 1} из ${totalSteps}`;
        }

        function validateStep(index) {
            const inputs = steps[index].querySelectorAll("input, select");
            for (const input of inputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    return false;
                }
            }
            return true;
        }

        nextBtn.addEventListener("click", () => {
            if (!validateStep(currentStep)) return;

            if (currentStep < totalSteps - 1) {
                currentStep++;
                showStep(currentStep);
            } else {
                submitForm();
            }
        });

        prevBtn.addEventListener("click", () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });

        function submitForm() {
            const formData = new FormData(form);
            formData.append("createdAt", new Date().toISOString().slice(0, 19).replace("T", " "));

            const data = new URLSearchParams();
            for (const pair of formData.entries()) {
                data.append(pair[0], pair[1]);
            }

            fetch("save_event.php", {
                method: "POST",
                body: data,
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
            })
                .then((response) => response.json())
                .then((result) => {
                    const resEl = document.getElementById("result");
                    resEl.innerText = result.success || result.error || "Ответ сервера не получен";
                    resEl.classList.add("show");
                    setTimeout(() => {
                        resEl.classList.remove("show");
                        form.reset();
                        currentStep = 0;
                        showStep(currentStep);
                        progressBar.style.width = "0%";
                    }, 3000);
                })
                .catch((error) => {
                    const resEl = document.getElementById("result");
                    resEl.innerText = "Ошибка: " + error.message;
                    resEl.classList.add("show");
                    setTimeout(() => {
                        resEl.classList.remove("show");
                    }, 3000);
                });
        }

        showStep(currentStep);
    </script>
</body>
</html>
