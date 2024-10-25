function nextStep(step) {
    // Collect form data at each step
    saveFormData();

    // Existing conditions
    if (step === 5) {
        const step1 = document.querySelector('input[name="lift"]:checked').value;
        const step2 = document.querySelector('input[name="deposit"]:checked').value;
        const step3 = document.querySelector('input[name="push"]:checked').value;
        const step4 = document.querySelector('input[name="pull"]:checked').value;
        const step5 = document.querySelector('input[name="carry"]:checked').value;

        if (step1 === "no" && step2 === "no" && step3 === "no" && step4 === "no" && step5 === "no") {
            alert("Devi rispondere Sì ad almeno una domanda tra gli Step 1 e Step 5 per continuare.");
            redirectToResult();
            return;  // Interrompi l'avanzamento
        }
    }

    if (step === 6) {
        const step6 = document.querySelector('input[name="weight"]:checked').value;
        if (step6 === "no") {
            alert("Devi rispondere Sì allo Step 6 per continuare.");
            redirectToResult();
            return;  // Interrompi l'avanzamento
        }
    }

    if (step === 7) {
        const step7 = document.querySelector('input[name="mechanical"]:checked').value;
        if (step7 === "yes") {
            alert("Devi rispondere No allo Step 7 per continuare.");
            redirectToResult();
            return;  // Interrompi l'avanzamento
        }
    }

    if (step === 23) {
        let answeredNo = false;
        const fieldsToCheck = [
            "pavement", "minimalHandling", "thermalEnv", "twoHands", "posture", 
            "onePerson", "gradualLift", "weight5to10", "upright", "closeToBody", 
            "weight3to5"
        ];
        
        for (const field of fieldsToCheck) {
            const answer = document.querySelector(`input[name="${field}"]:checked`).value;
            if (answer === "no") {
                answeredNo = true;
                break;
            }
        }

        if (!answeredNo) {
            alert("Devi rispondere No ad almeno una domanda tra gli Step 10 e Step 24 per continuare.");
            redirectToResult();
            return;  // Interrompi l'avanzamento
        }
    }

    if (step === 30) {
        let answeredYes = false;
        const fieldsToCheck = [
            "verticalDistance", "verticalDislocation", "horizontalDistance", "trunkRotation", 
            "lifting13min", "lifting11min", "lifting9min", "weightOver25", "weightOver20"
        ];
        
        for (const field of fieldsToCheck) {
            const answer = document.querySelector(`input[name="${field}"]:checked`).value;
            if (answer === "yes") {
                answeredYes = true;
                break;
            }
        }

        if (!answeredYes) {
            alert("Devi rispondere Sì ad almeno una domanda tra gli Step 25 e Step 31 per continuare.");
            redirectToResult();
            return;  // Interrompi l'avanzamento
        }
    }

    // Move to the next step
    document.getElementById(`step-${step}`).style.display = 'none';
    document.getElementById(`step-${step + 1}`).style.display = 'block';
}

function prevStep(step) {
    document.getElementById(`step-${step}`).style.display = 'none';
    document.getElementById(`step-${step - 1}`).style.display = 'block';
}

function saveFormData() {
    const formData = collectFormData();
    localStorage.setItem('formData', JSON.stringify(formData));

}

function collectFormData() {
    const formData = {};

    // Collect responses from various form fields
    const fields = [
        "lift", "deposit", "push", "pull", "carry", "weight", "mechanical", "pavement", 
        "minimalHandling", "thermalEnv", "twoHands", "posture", "onePerson", "gradualLift", 
        "weight5to10", "upright", "closeToBody", "weight3to5", "weight5to10Again", 
        "verticalDistance", "verticalDislocation", "horizontalDistance", "trunkRotation", 
        "lifting13min", "lifting11min", "lifting9min", "weightOver25", "weightOver20", "infoProvided", 
        "trainingProvided", "trainingManual"
    ];

    fields.forEach(field => {
        const element = document.querySelector(`input[name="${field}"]:checked`);
        if (element) {
            formData[field] = element.value;
        }
    });

    // Collect data from dynamically generated fields
    const rows = document.querySelectorAll(".object-row");
    const dynamicData = [];

    rows.forEach((row, index) => {
        const description = document.getElementById(`description-${index + 1}`).value;
        const numObjects = document.getElementById(`num-objects-${index + 1}`).value;
        const numLifts = document.getElementById(`num-lifts-${index + 1}`).value;
        const weight = document.getElementById(`weight-${index + 1}`).value;
        const duration = document.getElementById(`duration-${index + 1}`).value;
        const workers = document.getElementById(`workers-${index + 1}`).value;

        dynamicData.push({
            description: description,
            num_objects: numObjects,
            num_lifts: numLifts,
            weight: weight,
            duration: duration,
            workers: workers
        });
    });

    formData['dynamicData'] = dynamicData;

    return formData;
}

function redirectToResult() {
    saveFormData();
    window.location.href = 'result.html';
}

document.addEventListener('DOMContentLoaded', () => {
    console.log("Pagina caricata: Visualizza il primo step");
    document.getElementById('step-1').style.display = 'block';
});

document.getElementById("multiStepForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent form submission
    redirectToResult()
    alert("Le risposte sono state salvate in un file JSON.");
});