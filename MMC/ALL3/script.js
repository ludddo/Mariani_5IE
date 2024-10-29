function validateStep(step) {
    const stepElement = document.getElementById(`step-${step}`);
    const inputs = stepElement.querySelectorAll('input[type="radio"]');
    let isValid = false;

    inputs.forEach(input => {
        if (input.checked) {
            isValid = true;
        }
    });

    if (!isValid) {
        alert("Per favore, seleziona una risposta prima di procedere.");
    }

    return isValid;
}

function nextStep(step) {
    
    if (step != 8 && step != 9) 
    {
        if (!validateStep(step)) {
            return;  // Interrompi l'avanzamento se la validazione fallisce
        }
    }
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
            if (confirm("Devi rispondere Sì ad almeno una domanda tra gli Step 1 e Step 5 per continuare. Sei sicuro di voler terminare il questionario?")) {
                redirectToResult();
            }
            return;  // Interrompi l'avanzamento
        }
    }

    if (step === 6) {
        const step6 = document.querySelector('input[name="weight"]:checked').value;
        if (step6 === "no") {
            if (confirm("Devi rispondere Sì allo Step 6 per continuare. Sei sicuro di voler terminare il questionario?")) {
                redirectToResult();
            }
            return;  // Interrompi l'avanzamento
        }
    }

    if (step === 7) {
        const step7 = document.querySelector('input[name="mechanical"]:checked').value;
        if (step7 === "yes") {
            if (confirm("Devi rispondere No allo Step 7 per continuare. Sei sicuro di voler terminare il questionario?")) {
                redirectToResult();
            }
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
            if (confirm("Devi rispondere No ad almeno una domanda tra gli Step 10 e Step 24 per continuare. Sei sicuro di voler terminare il questionario?")) {
                redirectToResult();
            }
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
            if (confirm("Devi rispondere Sì ad almeno una domanda tra gli Step 25 e Step 31 per continuare. Sei sicuro di voler terminare il questionario?")) {
                redirectToResult();
            }
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

    // Check if dynamicData is empty
    if (dynamicData.length === 0) {
        dynamicData.push({
            description: "",
            num_objects: "",
            num_lifts: "",
            weight: "",
            duration: "",
            workers: ""
        });
    }

    formData['dynamicData'] = dynamicData;

    // Collect NIOSH data
    const altezzaElement = document.getElementById('altezza');
    const distanzaVerticaleElement = document.getElementById('distanzaVerticale');
    const distanzaOrizzontaleElement = document.getElementById('distanzaOrizzontale');
    const angoloElement = document.getElementById('angolo');
    const presaElement = document.getElementById('presa');
    const frequenzaElement = document.getElementById('frequenza');
    const numeroGestiElement = document.getElementById('numeroGesti');
    const gestoElement = document.getElementById('gesto');
    const operatoriElement = document.getElementById('operatori');
    const costanteDiPesoElement = document.getElementById('costanteDiPeso');
    const etaElement = document.getElementById('eta');

    if (altezzaElement && distanzaVerticaleElement && distanzaOrizzontaleElement && angoloElement && presaElement && frequenzaElement && numeroGestiElement && gestoElement && operatoriElement && costanteDiPesoElement && etaElement) {
        const altezza = altezzaElement.value;
        const distanzaVerticale = distanzaVerticaleElement.value;
        const distanzaOrizzontale = distanzaOrizzontaleElement.value;
        const angolo = angoloElement.value;
        const presa = presaElement.value;
        const frequenza = frequenzaElement.value;
        const numeroGesti = numeroGestiElement.value;
        const gesto = gestoElement.value;
        const operatori = operatoriElement.value;
        const costanteDiPeso = costanteDiPesoElement.value;
        const eta = etaElement.value;

        // Calculate NIOSH score
        const nioshScore = calculateNioshScore(altezza, distanzaVerticale, distanzaOrizzontale, angolo, presa, frequenza, numeroGesti, gesto, operatori, costanteDiPeso, eta);

        formData['nioshScore'] = nioshScore;
    } else {
        console.warn('Alcuni elementi NIOSH non sono stati trovati nel DOM.');
    }

    return formData;
}

function calculateNioshScore(altezza, distanzaVerticale, distanzaOrizzontale, angolo, presa, frequenza, numeroGesti, gesto, operatori, costanteDiPeso, eta) {
    var altezzaFattore = 0;
    var distanzaVerticaleFattore = 0;
    var distanzaOrizzontaleFattore = 0;
    var angoloFattore = 0;
    var presaFattore = 0;
    var frequenzaFattore = 0;
    var gestoFattore = 0;
    var operatoriFattore = 0;
    var CostantePesoASD = 20;

    if (altezza >= 0 && altezza <= 25) {
        altezzaFattore = 0.77;
    } else if (altezza > 25 && altezza <= 50) {
        altezzaFattore = 0.85;
    } else if (altezza > 50 && altezza <= 75) {
        altezzaFattore = 0.93;
    } else if (altezza > 75 && altezza <= 100) {
        altezzaFattore = 1;
    } else if (altezza > 100 && altezza <= 125) {
        altezzaFattore = 0.93;
    } else if (altezza > 125 && altezza <= 150) {
        altezzaFattore = 0.85;
    } else if (altezza > 150 && altezza <= 175) {
        altezzaFattore = 0.78;
    } else if (altezza > 175) {
        altezzaFattore = 0;
    }

    if (distanzaVerticale >= 0 && distanzaVerticale <= 25) {
        distanzaVerticaleFattore = 1;
    } else if (distanzaVerticale > 25 && distanzaVerticale <= 30) {
        distanzaVerticaleFattore = 0.97;
    } else if (distanzaVerticale > 30 && distanzaVerticale <= 40) {
        distanzaVerticaleFattore = 0.93;
    } else if (distanzaVerticale > 40 && distanzaVerticale <= 50) {
        distanzaVerticaleFattore = 0.91;
    } else if (distanzaVerticale > 50 && distanzaVerticale <= 70) {
        distanzaVerticaleFattore = 0.88;
    } else if (distanzaVerticale > 70 && distanzaVerticale <= 100) {
        distanzaVerticaleFattore = 0.87;
    } else if (distanzaVerticale > 100 && distanzaVerticale <= 175) {
        distanzaVerticaleFattore = 0.86;
    } else if (distanzaVerticale > 170) {
        distanzaVerticaleFattore = 0;
    }

    if (distanzaOrizzontale >= 0 && distanzaOrizzontale <= 25) {
        distanzaOrizzontaleFattore = 1;
    } else if (distanzaOrizzontale > 25 && distanzaOrizzontale <= 30) {
        distanzaOrizzontaleFattore = 0.83;
    } else if (distanzaOrizzontale > 30 && distanzaOrizzontale <= 40) {
        distanzaOrizzontaleFattore = 0.63;
    } else if (distanzaOrizzontale > 40 && distanzaOrizzontale <= 50) {
        distanzaOrizzontaleFattore = 0.50;
    } else if (distanzaOrizzontale > 50 && distanzaOrizzontale <= 55) {
        distanzaOrizzontaleFattore = 0.45;
    } else if (distanzaOrizzontale > 55 && distanzaOrizzontale <= 60) {
        distanzaOrizzontaleFattore = 0.42;
    } else if (distanzaOrizzontale > 60) {
        distanzaOrizzontaleFattore = 0;
    }

    if (angolo >= 0 && angolo <= 30) {
        angoloFattore = 1;
    } else if (angolo > 30 && angolo <= 60) {
        angoloFattore = 0.90;
    } else if (angolo > 60 && angolo <= 90) {
        angoloFattore = 0.81;
    } else if (angolo > 90 && angolo <= 120) {
        angoloFattore = 0.71;
    } else if (angolo > 120 && angolo < 135) {
        angoloFattore = 0.52;
    } else if (angolo == 135) {
        angoloFattore = 0.57;
    } else if (angolo > 135) {
        angoloFattore = 0;
    }

    if (presa == "buono") {
        presaFattore = 1;
    } else {
        presaFattore = 0.90;
    }

    if (frequenza == "continuo_meno_1_ora") {
        if (numeroGesti > 0 && numeroGesti <= 0.20) {
            frequenzaFattore = 1;
        } else if (numeroGesti > 0.20 && numeroGesti <= 1) {
            frequenzaFattore = 0.94;
        } else if (numeroGesti > 1 && numeroGesti <= 4) {
            frequenzaFattore = 0.84;
        } else if (numeroGesti > 4 && numeroGesti <= 6) {
            frequenzaFattore = 0.75;
        } else if (numeroGesti > 6 && numeroGesti <= 9) {
            frequenzaFattore = 0.52;
        } else if (numeroGesti > 9 && numeroGesti <= 12) {
            frequenzaFattore = 0.37;
        } else if (numeroGesti > 12 && numeroGesti <= 15) {
            frequenzaFattore = 0.12;
        } else if (numeroGesti > 15) {
            frequenzaFattore = 0;
        }
    } else if (frequenza == "continuo_1_2_ore") {
        if (numeroGesti > 0 && numeroGesti <= 0.20) {
            frequenzaFattore = 0.95;
        } else if (numeroGesti > 0.20 && numeroGesti <= 1) {
            frequenzaFattore = 0.88;
        } else if (numeroGesti > 1 && numeroGesti <= 4) {
            frequenzaFattore = 0.72;
        } else if (numeroGesti > 4 && numeroGesti <= 6) {
            frequenzaFattore = 0.50;
        } else if (numeroGesti > 6 && numeroGesti <= 9) {
            frequenzaFattore = 0.30;
        } else if (numeroGesti > 9 && numeroGesti <= 12) {
            frequenzaFattore = 0.21;
        } else if (numeroGesti > 12 && numeroGesti <= 15) {
            frequenzaFattore = 0.09;
        } else if (numeroGesti > 15) {
            frequenzaFattore = 0;
        }
    } else if (frequenza == "continuo_2_8_ore") {
        if (numeroGesti > 0 && numeroGesti <= 0.20) {
            frequenzaFattore = 0.85;
        } else if (numeroGesti > 0.20 && numeroGesti <= 1) {
            frequenzaFattore = 0.75;
        } else if (numeroGesti > 1 && numeroGesti <= 4) {
            frequenzaFattore = 0.45;
        } else if (numeroGesti > 4 && numeroGesti <= 6) {
            frequenzaFattore = 0.27;
        } else if (numeroGesti > 6 && numeroGesti <= 9) {
            frequenzaFattore = 0.52;
        } else if (numeroGesti > 9 && numeroGesti <= 12) {
            frequenzaFattore = 0.00;
        } else if (numeroGesti > 12 && numeroGesti <= 15) {
            frequenzaFattore = 0.00;
        } else if (numeroGesti > 15) {
            frequenzaFattore = 0;
        }
    }

    if (gesto == "si") {
        gestoFattore = 0.60;
    } else {
        gestoFattore = 1;
    }

    if (operatori == "no") {
        operatoriFattore = 1;
    } else {
        operatoriFattore = 0.85;
    }

    if (costanteDiPeso == "maschio") {
        if (eta < 18 || eta > 45) {
            CostantePesoASD = 20;
        }
        else {
            CostantePesoASD = 25;
        }
    } else {
        if (eta < 18 || eta > 45) {
            CostantePesoASD = 15;
        }
        else {
            CostantePesoASD = 20;
        }
    }

    //fai un alert con tutto anche per debug anche non fattori
    //alert("Altezza: " + altezza + "\nDistanza Verticale: " + distanzaVerticale + "\nDistanza Orizzontale: " + distanzaOrizzontale + "\nAngolo: " + angolo + "\nPresa: " + presa + "\nFrequenza: " + frequenza + "\nNumero Gesti: " + numeroGesti + "\nGesto: " + gesto + "\nOperatori: " + operatori + "\n\nAltezza Fattore: " + altezzaFattore + "\nDistanza Verticale Fattore: " + distanzaVerticaleFattore + "\nDistanza Orizzontale Fattore: " + distanzaOrizzontaleFattore + "\nAngolo Fattore: " + angoloFattore + "\nPresa Fattore: " + presaFattore + "\nFrequenza Fattore: " + frequenzaFattore + "\nGesto Fattore: " + gestoFattore + "\nOperatori Fattore: " + operatoriFattore);


    // Calcola il punteggio NIOSH peso limite
    const nioshScore = CostantePesoASD * altezzaFattore * distanzaVerticaleFattore * distanzaOrizzontaleFattore * angoloFattore * presaFattore * frequenzaFattore * gestoFattore * operatoriFattore;

    return nioshScore;
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
    const formData = collectFormData(); // Collect form data including NIOSH score
    localStorage.setItem('formData', JSON.stringify(formData)); // Save form data to localStorage
    redirectToResult();
});