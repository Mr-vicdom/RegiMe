host = ""

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

const removeNotice = () => {
    document.getElementById("notice").remove();
}

const isLogged = () => {
    // alert("al");
    $.ajax({
        type: 'get',
        url: host != "" ? host + "/php/register.php" : "./php/register.php", 
        dataType: 'json',
        data: "",
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
            if(data.type != 'error')
            {
                if(data.text == "redirect"){
                    window.location.replace("./profile.html");
                }
            }         
        }
    });
}

isLogged();


const loginUser = () => {
    const form = document.getElementById("login-fm");
    
    
    const f = new FormData(form);
    
    let data = "";
    
    for (const [key,value] of f) {
        data+=key+"="+value+"&";
    }
    // data = "fname=Vignesh&lname=Arumugam&contact=1234567890&dob=2023-03-16&email=thirukriz08@gmail.com&password=asdfghjkl&";
    console.log(data);


    $.ajax({
        type: 'post',
        url: host != "" ? host + "/php/login.php" : "./php/login.php", 
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
                let err = document.getElementById("err");
                if(data.text == "Input fields are empty!" || data.text == "Invalid email format" || data.text == "Password should be >= 8 and <= 16"){
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
                document.body.innerHTML += `<div id="notice" class="notice"> <div class="line"></div>
                        <div class="content">${data.text}</div>
                        <button onclick="removeNotice()">❌</button></div>`
                setTimeout(() => {
                    removeNotice();
                },6000);
            }          
        }
    });
}