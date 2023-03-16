data = "profile";


$.ajax({
    type: 'post',
    url: "./php/profile.php", 
    dataType: 'json',
    data: data,
    beforeSend: function() {
        alert(data);
        $('#submit').attr('disabled', true);
    },
    complete: function() {
        alert('af');
        $('#submit').attr('disabled', false);
    },  
    success: function(data)
    {
        if(data.type == 'error')
        {
            alert(data.text);
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