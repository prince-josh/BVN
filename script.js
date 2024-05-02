document.getElementById('generateBtn').addEventListener('click', generateBVN);
document.getElementById('printBtn').addEventListener('click', printInfo);

function generateBVN() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const photo = document.getElementById('photo').files[0];

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('photo', photo);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'generate_bvn.php', true);
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE) {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    document.getElementById('result').textContent = 'BVN generated successfully: ' + response.bvn;
                    document.getElementById('printBtn').classList.remove('hidden');
                    showPrintInfo(name, email, phone, response.bvn, response.photoPath);
                } else {
                    document.getElementById('result').textContent = response.message;
                }
            } else {
                document.getElementById('result').textContent = "An error occurred. Please try again.";
            }
            document.getElementById('loading').classList.add('hidden');
        }
    };
    xhr.send(formData);

    document.getElementById('result').textContent = '';
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('printBtn').classList.add('hidden');
    hidePrintInfo();
}

function showPrintInfo(name, email, phone, bvn, photoPath) {
    document.getElementById('printName').textContent = name;
    document.getElementById('printEmail').textContent = email;
    document.getElementById('printPhone').textContent = phone;
    document.getElementById('printBVN').textContent = bvn;
    document.getElementById('printPhoto').src = photoPath;
    document.getElementById('printContainer').classList.remove('hidden');
}

function hidePrintInfo() {
    document.getElementById('printName').textContent = '';
    document.getElementById('printEmail').textContent = '';
    document.getElementById('printPhone').textContent = '';
    document.getElementById('printBVN').textContent = '';
    document.getElementById('printPhoto').src = '';
    document.getElementById('printContainer').classList.add('hidden');
}

function printInfo() {
    window.print();
}