//Get the button
var topBtn = document.getElementById("topBtn");
var downBtn = document.getElementById("downBtn");
// When the user scrolls down 20px from the top of the document, show the button
$('#div-modal').scroll(function() {
    scrollFunction()
});
scrollingElement = (document.scrollingElement || document.body)

function scrollFunction() {
    if ($('#div-modal').scrollTop() > 20) {
        topBtn.style.display = "block";
        downBtn.style.display = "none";
    } else {
        downBtn.style.display = "block";
        topBtn.style.display = "none";
    }
}
// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    $('#div-modal').animate({
        scrollTop: 0
    }, 500);
    downBtn.style.display = "block";
    topBtn.style.display = "none";
}

function bottomFunction() {
    $('#div-modal').animate({
        scrollTop: $('#div-modal').prop("scrollHeight")
    }, 500);
    topBtn.style.display = "block";
    downBtn.style.display = "none";
}
// END BUTTON

$(document).ready(function(){
    // $('#CurrentLocation').on('click',function(e){
    //     e.preventDefault()
    //     initializeMap()
    // })

    $('#Area').on('change', function(){
        if($('#onloadArea').length){
            $('#onloadArea').remove()
        }
        // clear checkin/out , report
        $('#TimeCheckin').html('')
        $('#TimeCheckout').html('')
        $('#ContentReport').val('')
        if($(this).val() != "-1"){
            // append customer
            // $("#Area option[value='-1']").remove()
            appendCustomer()
            setTimeout(function(){
                checkTimecardOfCustomer()
            },1000)
        } else {
            $('#CustomerName').html('<option value="-1"></option>')
        }
    })

    $('#CustomerName').on('change',function(){
        if($(this).val() != "-1"){
            checkTimecardOfCustomer()
        }
    })

    $('#CheckIn').on('click', function(e){
        e.preventDefault()
        initializeMap()
        setTimeout(function(){
            var allow_location = ($('#currentCoord').val() != "none") ? true : false
            if(allow_location){
                $(this).attr('disabled', true)
                if($('#Area').val() == "-1"){
                    // TODO:^language
                    alert($('#text_please_choose_area').val())
                    $(this).attr('disabled', false)
                } else {
                    if($('#CustomerName').val() == "-1"){
                        // TODO:^language
                        alert($('#text_please_choose_customer').val())
                        $(this).attr('disabled', false)
                    } else {
                        checkIn()
                    }
                }
            } else {
                swal({
                    title: '',
                    text: $("#text_enable_location").val(),
                    button: 'OK'
                })
            }
        },500)
    })

    $('#Report').on('click',function(e){
        e.preventDefault()
        if($('#Area').val() == "-1"){
            // TODO:^language
            alert($('#text_please_choose_area').val())
        } else {
            if($('#CustomerName').val() == "-1"){
                // TODO:^language
                alert($('#text_please_choose_customer').val())
            } else {
                validateReport()
            }
        }
    })

    $('#ClearReport').on('click', function(e){
        e.preventDefault()
        $('#ContentReport').val('')
        $('#NoteReport').val('')
        if($('.checkbox-report').length){
            $('.checkbox-report').prop('checked', false)
        }
        $('#previewImages').html('')
        files = []
    })

    $('#SubmitReport').on('click', function(e){
        e.preventDefault()
        $(this).attr('disabled', true)
        if($('.not-check').css('display') == 'block' && $('#ContentReport').val() == ""){
            // TODO:^language
            alert($('#text_required_report').val())
            $(this).attr('disabled', false)
        } else {
            insertReport()
        }

    })

    $('#CheckOut').on('click',function(e){
        e.preventDefault()
        initializeMap()
        setTimeout(function(){
            var allow_location = ($('#currentCoord').val() != "none") ? true : false
            if(allow_location){
                $(this).attr('disabled', true)
                if($('#Area').val() == "-1"){
                    // TODO:^language
                    alert($('#text_please_choose_area').val())
                    $(this).attr('disabled', false)
                } else {
                    if($('#CustomerName').val() == "-1"){
                        // TODO:^language
                        alert($('#text_please_choose_customer').val())
                        $(this).attr('disabled', false)
                    } else {
                        checkOut()
                    }
                }
            } else {
                swal({
                    title: '',
                    text: $("#text_enable_location").val(),
                    button: 'OK'
                })
            }
        },500)
    })

    $('#CaptureCamera').on('click',function(e){
        e.preventDefault()
        $('#lookCameraModal').modal('hide')
        if($(this).data('type') == 'checkin'){
            $('#btnInsertCheckin').click()
        } else {
            $('#btnInsertCheckout').click()
        }
    })

    $('#WorkingCalendar').on('click',function(e){
        e.preventDefault()
        location.href = __baseUrl + 'calendar'
    })

    $('#TypeReport').on('change', function(){
        if($('#onloadTypeReport').length){
            $('#onloadTypeReport').remove()
        }
        resetFieldReport()
    })

    $(document).on('click', '.checkbox-report', function(){
        postCheckbox($('#IDTimeCard').val(), $('#TypeReport').val(), $(this).val(), $(this).prop('checked'))
    })
})

function getListArea(){
    $.ajax({
        url: __baseUrl + 'mypage/getArea',
        type: 'get',
        success: function(response){
            var list = ''
            $.each(response.areas, function(index, value){
                list +=  '<option value="'+ value.AreaID +'">'+ value.Name +'</option>'
            })
            $('#Area').html(list)
        }
    })
}

function appendCustomer(){
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrfToken},
        url: __baseUrl + 'mypage/getCustomer',
        type: 'post',
        data: {AreaID: $('#Area').val()},
        success: function(response){
            var list = ''
            $.each(response, function(index, value){
                list +=  '<option value="'+ value.CustomerID +'">'+ value.Name +'</option>'
            })
            $('#CustomerName').html(list)
        }
    })
}

function checkTimecardOfCustomer(){
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrfToken},
        url: __baseUrl + 'mypage/checkTimecardOfCustomer',
        type:'POST',
        data: {'customerID': $('#CustomerName').val()},
        success:function(res){
            if(res.timecard){
                $('#TimeCheckin').html(res.timeCheckin)
                $('#TimeCheckout').html(res.timeCheckout)
            } else {
                $('#TimeCheckin').html('')
                $('#TimeCheckout').html('')
            }
        }
    })
}

function initializeMap() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError, {
            timeout: 10000
        });
    } else {
        alert("Geolocation is not supported by this browser.")
    }

    function showPosition(position) {
        let lat = position.coords.latitude
        let lng =  position.coords.longitude
        $('#currentCoord').val(lat + "," + lng)
    }

    function showError(error) {
        console.log(error)
    }
}

function checkIn(){
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrfToken},
        url: __baseUrl + 'mypage/validateCheckin',
        type: 'POST',
        data: {customerID: $('#CustomerName').val()},
        success: function(res){
            $('#CheckIn').attr('disabled', false)
            if(res.valid){
                $('#CaptureCamera').data('type','checkin')
                $('#lookCameraModal').modal()
            } else {
                if(res.same_area == 1){
                    swal({
                        // TODO:^language
                        title: $('#text_check_in').val(),
                        text: $('#text_you_checked_in').val().replace("TIME", res.timeCheckin),
                        icon: 'info'
                    })
                } else {
                    swal({
                        // TODO:^language
                        title: $('#text_check_in').val(),
                        text: $('#text_not_checked_out').val() + '\n' + res.customerName + '(' + res.areaName + ')',
                        icon: 'info',
                        button: 'OK'
                        // buttons:true,
                        // dangerMode: true,
                    })
                    .then((continueCheckin) => {
                        if(continueCheckin){
                            $('#Area').val(res.areaID)
                            appendCustomer()
                            setTimeout(function(){
                                $('#CustomerName').val(res.customerID)
                            },500)
                            $('#TimeCheckin').html(res.timeCheckin)
                        }
                    })
                }
            }
        }
    })
}

function validateReport(){
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrfToken},
        url: __baseUrl + 'mypage/validateReport',
        type: 'POST',
        data: {customerID:$('#CustomerName').val()},
        success:function(res){
            if(res.NullCheckin){
                //TODO:^language
                swal({
                    title: $('#text_report').val(),
                    text: $('#text_not_checked_in').val(),
                    icon: 'error'
                })
            } else {
                // refresh form
                $('#ContentReport').val('')
                $('#ContentReport').attr('placeholder',$('#text_required_report').val())
                $('#NoteReport').val('')
                $('#NoteReport').attr('placeholder',$('#text_required_report').val())
                $('#previewImages').html('')
                files = []

                var options = ''
                if($('#onloadTypeReport').length < 1){
                    options += '<option value="-1" id="onloadTypeReport" selected></option>'
                }
                options += $('#TypeReport').html()
                $('#TypeReport').html(options)

                $('#TypeSubmit').val(res.TypeSubmit)
                $('#IDReport').val(res.IDReport)
                $('#IDTimeCard').val(res.IDTimeCard)

                if(res.TypeCode){
                    if($('#onloadTypeReport').length){
                        $('#onloadTypeReport').remove()
                    }
                    $('#TypeReport').val(res.TypeCode)
                    resetFieldReport(res.Check, res.Content)
                } else {
                    $('.report-check').css('display', 'none')
                    $(".not-check").css('display', 'none')
                }

                if(res.images){
                    var i = 0
                    $.each(res.images, function(index,value){
                        var tplImageUploaded = $('#tplImageUploaded').html()
                        tplImageUploaded = tplImageUploaded
                                .replace(/__id__/g, i)
                                .replace(/__id-uploaded__/g,value.ID)
                                .replace(/__src__/g, "ImageReport/ID_" + res.IDReport + "/" + value.ImageName)
                        $('.files-preview').append(tplImageUploaded)

                        i++
                    })
                    $('#currentIndexFiles').val(i)

                }

                setTimeout(function(){
                    $('#previewImages').show()
                },1000)
                $('#reportModal').modal()
            }
        }
    })
}

function insertReport(){
    const form = $(".ajax-form-report");
    ajax_form(form);
    $('.loader').show()
    $('.text-submit').hide()
}

function ajax_form(form){
    const form_data = new FormData(form.get(0));

    const button = form.find("button[id=SubmitReport]");
    button.attr("disabled", true);

    // case update
    $arr_uploaded = []
    if($('#TypeSubmit').val() == 'update'){
        $('.image-uploaded').each(function(){
            $arr_uploaded.push($(this).attr('data-id-uploaded'))
        })
        form_data.append('imagesUploaded', $arr_uploaded)
    }

    if(files.length == 0){
        form_data.append('files', 'null');
    } else {
        files.forEach(file => {
            /* here just put file as file[] so now in submitting it will send all files */
            form_data.append('files[]', file);
        });
    }

    var haveCheck = ($('.report-check').css('display') == "none") ? 0 : 1

    var arrChecked = []
    var i = 0
    $('input[name="Check"]:checked').each(function(){
        arrChecked.push($(this).val())
    })

    form_data.append('typeSubmit', $('#TypeSubmit').val())
    form_data.append('typeReport', $('#TypeReport').val())
    form_data.append('customerID', $('#CustomerName').val())
    form_data.append('ID', $('#IDReport').val())
    form_data.append('TimeCardID', '')
    form_data.append('content', $('#ContentReport').val())
    form_data.append('haveCheck', haveCheck)
    form_data.append('valuesChecked', arrChecked)
    form_data.append('note', $('#NoteReport').val())

    $.ajax({
        headers:{'X-CSRF-TOKEN': csrfToken},
        url: __baseUrl + 'mypage/insertReport',
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        dataType: "json",
        success:function(res){
            if(res.success == 1){
                files = []
                $('#reportModal').modal('hide')
                swal({
                    // TODO:^language
                    title: $('#text_report').val(),
                    text: $('#text_submit_successfully').val(),
                    icon: 'success'
                })
                setTimeout(function(){
                    swal.close()
                },3000)
            }
            $('#SubmitReport').attr('disabled', false)
            $('.loader').hide()
            $('.text-submit').show()
        },
        error:function(res){
            swal({
                // TODO:^language
                title: $('#text_report').val(),
                text: $('#text_submit_failed').val(),
                icon: 'error'
            })
            $('#SubmitReport').attr('disabled', false)
            $('.loader').hide()
            $('.text-submit').show()
        }
    })
}

function checkOut(){
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrfToken},
        url: __baseUrl + 'mypage/validateCheckout',
        type: 'POST',
        data: {customerID: $('#CustomerName').val()},
        success: function(res){
            $('#CheckOut').attr('disabled', false)
            if(res.valid){
                if(res.valid == 1){
                    $('#timecardIDCheckout').val(res.timecardIDCheckout)
                    $('#CaptureCamera').data('type','checkout')
                    $('#lookCameraModal').modal()
                } else {
                    swal({
                        // TODO:language
                        title: $('#text_check_out').val(),
                        text: res.info,
                        icon: 'error'
                    })
                }
            } else {
                if(res.not_reported){
                    swal({
                        // TODO:^language
                        title: $('#text_check_out').val(),
                        text: $('#text_not_reported').val(),
                        icon: 'error'
                    })
                } else {
                    if(res.same_area == 0){
                        // if(res.not_timeout){ // diff area + not TimeOut
                        //     swal({
                        //         // TODO:^language
                        //         title: $('#text_check_out').val(),
                        //         // text: 'The Customer Name is selecting and check-in are not the same / You have not checked in yet at here.',
                        //         text: $('#text_not_checked_in').val(),
                        //         icon: 'error'
                        //     })
                        // } else { // diff area + Timeout
                        //     swal({
                        //         // TODO:^language
                        //         title: $('#text_check_out').val(),
                        //         text: $('#text_not_checked_in').val(),
                        //         icon: 'error'
                        //     })
                        // }
                        swal({
                            // TODO:^language
                            title: $('#text_check_out').val(),
                            text: $('#text_not_checked_in').val(),
                            icon: 'error'
                        })
                    } else {
                        swal({
                            // TODO:^language
                            title: $('#text_check_out').val(),
                            text:  $('#text_you_checked_out').val().replace("TIME",res.timeCheckout),
                            icon: 'info'
                        })
                    }
                }
            }
        }
    })
}

function resetFieldReport(checked = [], content = ""){
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrfToken},
        url: __baseUrl + 'mypage/getFormReport',
        type: 'POST',
        data: {'id_type': $('#TypeReport').val()},
        success:function(response){
            if(response.typeCheck == 1){
                var data = response.data
                var rows = ''
                const tplCheckbox = $('#tplCheckbox').html()
                const tplSecCheck = $('#tplSecCheck').html()

                var secCheck = ''
                var checkboxs = ''
                var category = ''
                var haveCategory = 0

                $.each(data, function(index, child){
                    secCheck = ''
                    checkboxs = ''
                    category = index
                    haveCategory = (index != "") ? haveCategory + 1  : haveCategory
                    secCheck = tplSecCheck.replace(/__category__/g,category)
                    $.each(child,function(index, val){
                        checkboxs += tplCheckbox.replace(/__id__/g,val.CheckID)
                                            .replace(/__checkcode__/g,val.CheckCode)
                                            .replace(/__detail__/g,val.CheckPoint)

                    })
                    secCheck = secCheck.replace(/__checkboxs__/g,checkboxs)

                    rows += secCheck
                })

                $("#checkreport").html(rows)

                //set values
                $.each(checked, function(index, value){
                    $('.checkbox-report').each(function(){
                        var this_value = $(this).val()
                        if(this_value == value.CheckID && value.Result == 1){
                            $(this).prop('checked', true)
                        }
                    })
                })
                if(content != "null"){
                    $('#NoteReport').val(content)
                }

                // handle html
                if(haveCategory == 0){
                    $('.legend-category').css('display', 'none')
                }
                $('.report-check').css('display', 'block')
                $(".not-check").css('display', 'none')
            } else {
                if(content != "null"){
                    $('#ContentReport').val(content)
                }
                $('.report-check').css('display', 'none')
                $(".not-check").css('display', 'block')
            }
        }
    })
}

function postCheckbox(timecard, type, id, checked){
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrfToken},
        url: __baseUrl + 'mypage/queryCheck',
        type: 'POST',
        data: {'timecard': timecard, 'type': type, 'id': id, 'checked': checked},
        success:function(res){
            if(res.success == 1){
                console.log('ok')
            }
        }
    })
}
