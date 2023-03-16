const inputs = document.querySelectorAll("form .field input");
const labels = document.querySelectorAll("form .field label");

for (let i = 0; i < inputs.length; i++) {
    inputs[i].addEventListener("focus",() => {
        document.getElementById("err").innerText = "";
    })
    inputs[i].addEventListener("change",(e) => {
        if(e.target.value != ""){
            labels[i].classList.add("active");
        } else {
            labels[i].classList.remove("active");
        }
    });
}


const showPass = () => {
    document.getElementById('password').type = "text";
    document.getElementById('show').classList.add("active");
    document.getElementById('hide').classList.remove("active");
}
const hidePass = () => {
    document.getElementById('password').type = "password";
    document.getElementById('hide').classList.add("active");
    document.getElementById('show').classList.remove("active");
}


data = "profile";

const update = () => {
    document.getElementById("update").classList.remove("deactive");
    document.getElementById("edit").classList.add("deactive");
}
const edit = () => {

    const form = document.getElementById("update-fm");
    
    
    const f = new FormData(form);
    
    let data = "";
    
    for (const [key,value] of f) {
        data+=key+"="+value+"&";
    }

    $.ajax({
        type: 'post',
        url: "./php/profile.php", 
        dataType: 'json',
        data: data,
        beforeSend: function() {
            // alert(data);
            $('#submit').attr('disabled', true);
        },
        complete: function() {
            // alert('af');
            $('#submit').attr('disabled', false);
        },  
        success: function(data)
        {
            // alert(data);
            if(data.type == 'error')
            {
                let err = document.getElementById("err");
                if(data.text == "Input fields are empty!" || data.text == "Invalid email format" || data.text == "Invalid contact number" || data.text == "Password should be >= 8 and <= 16"){
                    err.innerText = data.text;
                } else {
                    document.body.innerHTML += `<div id="notice" class="notice"> <div class="line"></div>
                        <div class="content">${data.text}</div>
                        <button onclick="removeNotice()">❌</button></div>`
                setTimeout(() => {
                    removeNotice();
                },6000);
                }
            }else{
                if(data.text == "Record updated successfully"){
                    window.location.replace("./profile.html");
                } else{
                    document.body.innerHTML += `<div id="notice" class="notice"> <div class="line"></div>
                        <div class="content">Record update failed</div>
                        <button onclick="removeNotice()">❌</button></div>`
                setTimeout(() => {
                    removeNotice();
                },6000);
                }
            }          
        }
    });

}

const logout = () => {
    $.ajax({
        type: 'delete',
        url: "./php/profile.php", 
        dataType: 'json',
        data: '',
        beforeSend: function() {
            // alert(data);
            $('#submit').attr('disabled', true);
        },
        complete: function() {
            // alert('af');
            $('#submit').attr('disabled', false);
        },  
        success: function(data)
        {
            // alert(data);
            if(data.type != 'error')
            {
                if(data.text == "Record deleted successfully"){
                    window.location.replace("./index.html");
                } else{
                    document.body.innerHTML += `<div id="notice" class="notice"> <div class="line"></div>
                        <div class="content">Record deletion failed</div>
                        <button onclick="removeNotice()">❌</button></div>`
                setTimeout(() => {
                    removeNotice();
                },6000);
                }
            }          
        }
    });
}

$.ajax({
    type: 'get',
    url: "./php/profile.php", 
    dataType: 'json',
    data: data,
    beforeSend: function() {
        // alert(data);
        $('#submit').attr('disabled', true);
    },
    complete: function() {
        // alert('af');
        $('#submit').attr('disabled', false);
    },  
    success: function(data)
    {
        if(data.type == 'error')
        {
            document.body.innerHTML += `<div id="notice" class="notice"> <div class="line"></div>
                        <div class="content">${data.text}</div>
                        <button onclick="removeNotice()">❌</button></div>`
                setTimeout(() => {
                    removeNotice();
                },6000);
        }else{
            data = data.text;
            let details = document.getElementById("details");
            for (const k in data) {
                console.log(data[k]);
                let txt = `<div class="data">
                <div class="label">${k}</div>
                <div class="colon">:</div>
                <div class="value">${data[k]}</div></div>`;
                details.innerHTML += txt;
            }
        }          
    }
});