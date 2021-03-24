$('.modal-dialog').draggable({
    handle: ".modal-header"
});

// $('#btnHoldMove').click(function(e){
//     e.preventDefault()
// })

// var tmpAreaChecked = ''

$('#modalStaff').on('hidden.bs.modal', function(e) {
    // do something...
    $('#checkboxes').hide()
    expanded = false;
    // $('.areaChecked').html('')
})

$('.btnCloseCheckboxArea').on('click', function(e) {
    e.preventDefault()
    $('#checkboxes').hide()
    expanded = false;

    // append
    var i = 0
    var checked = ''
    var values = ''
    $("input:checkbox[name=Area]:checked").each(function() {
        if (i == 0) {
            checked += $(this).closest('label').find('span').html()
            values += $(this).closest('label').find('input').val()
        } else {
            checked += ", " + $(this).closest('label').find('span').html()
            values += "," + $(this).closest('label').find('input').val()
        }
        i++
    });
    $('.areaChecked').html(checked)
    $('.valuesChecked').val(values)
})



// $('#modalStaff').on('show.bs.modal', function (e) {
//     // do something...
//     $('#checkboxes').show()
// })
var table = $('#serverDataTable').DataTable({
    "dom": '<"pull-left"fi>tp',
    "processing": true,
    "serverSide": true,
    "ajax": {
        headers: { 'X-CSRF-TOKEN': __csrfToken },
        url: __baseUrl + 'admin/staff',
        type: 'POST'
    },
    "columns": [{
            "data": null,
            "sortable": false,
            orderable: false,
            "sClass": "text-right",
            render: function(data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        { "data": "StaffID" },
        { "data": "Name" },
        { "data": "Password", orderable: false },
        { "data": "Position", "sClass": "text-center" },
        { "data": "Area", "sClass": "area-text" },
        {
            "data": "Region",
            "sClass": "text-center",
            render: function(data, type, row) {
                if (row.Region == "S") { return "South" } else if (row.Region == "M") { return "Middle" } else if (row.Region == "N") { return "North" }
                return ""
            }
        },
        {
            "data": "CreatedDate",
            "sClass": "text-center",
            render: function(data, type, row) {
                if (type === "sort" || type === "type") {
                    return data;
                }
                return moment(data).format("YYYY/MM/DD HH:mm");
            }
        },

        {
            data: "ID",
            orderable: false,
            sClass: "text-center",
            render: function(data, type, row) {
                return "" +
                    "<input type=\"hidden\" id=\"ID\" value=\"" + row.ID + "\">\n" +
                    "<button type=\"button\" data-toggle=\"tooltip\" title=\"Edit\" class=\"btn btn-info btnEditStaff\" data-areaId=\"" + row.AreaID + "\"><i class=\"far fa-edit\"></i></button>\n" +
                    "<button type=\"button\" data-toggle=\"tooltip\" title=\"Delete\" class=\"btn btn-danger btnDeleteStaff\"><i class=\"far fa-trash-alt\"></i></button>\n" +
                    "<button type=\"button\" data-toggle=\"tooltip\" title=\"QR Code\" class=\"btn btn-dark btnQrCode\"><i class=\"fas fa-qrcode\"></i></button>\n" +
                    "<button type=\"button\" data-toggle=\"tooltip\" title=\"ID Card\" class=\"btn btn-success btnIdCard\"><i class=\"fas fa-id-card\"></i></button>";
            }
        },
    ],
    "searching": true,
    "paging": true,
    "pageLength": 100,
    "info": true,
    "order": [
        [1, 'asc']
    ],
});

$(document).ready(function() {
    // ************* DATATABLE *************


    $('[data-toggle="tooltip"]').tooltip();

    // ************* SHOW ADD STAFF MODAL *************
    $('#show-modal-staff').on('click', function() {
            clearStaffForm()
            $('.areaChecked').html('')
            $('.valuesChecked').val('')
            $('#modalStaff').modal()
        })
        /* upload images yellow card*/
    $('#gallery-photo-add').on('change', function() {
        imagesPreview(this, 'div.gallery');
        setTimeout(function() {
            if ($('.files-selected-not-allow').length) {
                $("#btnClearFileType").click();
                alert('Please select only image file.');
            }
            swal.close()
        }, 500);
    });
    /* clear file type images */
    $("#btnClearFileType").on('click', function(e) {
        e.preventDefault();
        $('#gallery-photo-add').val('');
    });
    $(document).on('click', '.btn-clear-image', function(e) {
        e.preventDefault();
        $('#gallery-photo-add').val('');
        $('.gallery').html('');
    })
    $(document).on('click', '.btn-clear-image-old', function(e) {
        e.preventDefault();
        $('.gallery').html('<input id="clearedImageOld" hidden />');
    })

    // ************* SHOW EDIT STAFF MODAL *************
    $('#tblStaff').on('click', '.btnEditStaff', function() {
        let id = $(this).closest('tr').find("#ID").val()
        $('.areaChecked').html($(this).closest("tr").find('.area-text').html())
        $('.valuesChecked').val($(this).attr('data-areaId'))

        formEdit(id)
    })

    // *************** SUBMIT ADD/EDIT STAFF ****************
    $('#btnSubmitStaff').on('click', function(e) {
        e.preventDefault()
        putStaff()
    })

    // **************** DELETE STAFF *************
    $('#tblStaff').on('click', '.btnDeleteStaff', function() {
        swal({
                title: "Are you sure you want to delete this staff?",
                icon: false,
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    let id = $(this).closest('tr').find("#ID").val()
                    delStaff(id)
                }
            })

    })
});

/**
 *
 */
function clearStaffForm() {
    $('#id_staff').val('')

    $('#StaffID').show()
    $('.id-helper').show()
    $('#spanStaffID').html('')
    $('#spanStaffID').css('display', 'none')

    $('#StaffID').val('')
    $('#Name').val('')
        // $('input[name="Position"]').prop('checked', false)
    $('#Position').val('-1')
    $('.form-area').attr('style', 'none !important')
    $("input:checkbox[name=Area]:checked").each(function() {
        $(this).prop('checked', false)
    });
}

/**
 *
 * @param id
 */
function formEdit(id) {
    clearStaffForm()
    $.ajax({
        headers: { 'X-CSRF-TOKEN': __csrfToken },
        url: __baseUrl + 'admin/staff/search',
        type: 'post',
        data: { 'id_staff': id },
        success: function(response) {
            if (response.success) {
                const data = response.data
                const areas = response.areas

                $('#StaffID').hide()
                $('.id-helper').hide()
                $('#spanStaffID').html(data.StaffID)
                $('#spanStaffID').css('display', 'block')
                $('#Name').val(data.Name)
                $('#Position').val(data.Position)
                $('#id_staff').val(data.ID)
                $('#Password').val(data.Password)
                $('#Title').val(data.Title)
                $('#Region').val(data.Region)
                    // show image
                $('#gallery-photo-add').val('')
                if (data.Image && data.Image != "") {
                    $('.gallery').html('<img src="' + __baseUrl + "files/StaffImage/" + data.Image + '" class="img-zoom" style="width:100px"><button class="btn btn-danger btn-clear-image-old">X</button>')
                } else {
                    $('.gallery').html('')
                }

                if (data.Position == 'Area Leader' || data.Position == 'Leader') {
                    $('.form-area').attr('style', 'display:flex !important')
                    $.each(areas, function(index, value) {
                        $("input:checkbox[name=Area]").each(function() {
                            if ($(this).val() == value.AreaID) {
                                $(this).prop('checked', true)
                            }
                        });
                    })

                }

                $('#modalStaff').modal()
            }
        },
        error: function(response) {
            console.log(response)
        }
    })
}

/**
 *
 */
function putStaff() {
    let check = validateform()
    if (check) {
        const form = $("#put_staff");
        swal({ title: "Please wait! Submiting...", buttons: false, closeOnClickOutside: false })
        const form_data = new FormData(form.get(0));

        const button = form.find("button[type=submit]");
        button.attr("disabled", true);

        if ($('#changedImage').length > 0) {
            var file_data = $(".ajax-multiple-file").prop("files")[0];
            form_data.append("files[]", file_data);
        }

        form_data.append("clearImage", "false");
        if ($('#clearedImageOld').length > 0) {
            form_data.append("clearImage", "true");
        }

        var areas = []
        $("input:checkbox[name=Area]:checked").each(function() {
            areas.push($(this).val());
        });


        form_data.append("ID", $('#id_staff').val());
        form_data.append("Name", $('#Name').val());
        form_data.append("Password", $('#Password').val());
        form_data.append("Position", $('#Position').val());
        form_data.append("Title", $('#Title').val());
        form_data.append("Region", $('#Region').val());
        form_data.append("Areas", areas);
        form_data.append("Area", $('.valuesChecked').val());
        form_data.append("IDStaff", $('#spanStaffID').html());
        form_data.append("currIndexSort", $("#currIndexSort").val());
        form_data.append("currDirSort", $('#currDirSort').val());

        // add new
        if ($('#spanStaffID').html() == "") {
            form_data.append("StaffID", $('#StaffID').val());
            form_data.append("IDStaff", $('#StaffID').val());
        }
        $.ajax({
            url: __baseUrl + 'admin/staff/edit',
            headers: { 'X-CSRF-Token': __csrfToken },
            type: 'post',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            dataType: "json",
            success: function(response) {
                if (parseInt(response.status) === 1) {
                    $('#modalStaff').modal('hide')
                    clearStaffForm()
                        // loadStaffTable(response.lst_staffs)
                    swal({
                        title: "Successfully!",
                        icon: "success",
                    })
                    table.order([
                        [Number($('#currIndexSort').val()), $('#currDirSort').val()]
                    ]).draw()
                } else {
                    swal({
                        title: "Have error. Please double check that the staff ID is not duplicates in the database.",
                        icon: "error",
                    });
                }
                button.attr("disabled", false);
            },
            error: function(response) {
                console.log(response)
                swal({
                    title: "Have error. Please double check that the staff ID is not duplicates in the database.",
                    icon: "error",
                });
                button.attr("disabled", false);
            }
        })
    }
}

/**
 *
 * @param id
 */
function delStaff(id) {
    $.ajax({
        headers: { 'X-CSRF-TOKEN': __csrfToken },
        url: __baseUrl + 'admin/staff/delete',
        type: 'post',
        data: { 'id_staff': id },
        success: function(response) {
            if (response.status) {
                // loadStaffTable(response.lst_staffs)
                swal({
                        'title': 'Deleted Successfully!',
                        'icon': 'success'
                    })
                    .then((OK) => {
                        location.reload()
                    });
            }
        },
        error: function(response) {
            console.log(response)
        }
    })
}

/**
 *
 * @param lst_staffs
 */
function loadStaffTable(lst_staffs) {
    $('#dataTable').DataTable().clear().draw();
    var table = $('#dataTable').DataTable();
    for (i = 0; i < lst_staffs.length; i++) {
        table.rows.add($(
            '<tr>' +
            '<td class="text-center">' + lst_staffs[i].StaffID + '</td>' +
            '<td>' + lst_staffs[i].Name + '</td>' +
            '<td class="text-center">' + lst_staffs[i].Position + '</td>' +
            '<td class="text-center">' + lst_staffs[i].CreatedDate + '</td>' +
            '<td class="text-center w-10">\n' +
            '    <input type="hidden" id="ID" value="' + lst_staffs[i].ID + '">\n' +
            '    <button type="button" class="btn btn-info btnEditStaff"><i class="far fa-edit"></i></button>\n' +
            '    <button type="button" class="btn btn-danger btnDeleteStaff"><i class="far fa-trash-alt"></i></button>\n' +
            '</td>' +
            '</tr>'
        )).draw();
    }
}

function validateform() {
    if ($('#spanStaffID').html() == "" && !$('#StaffID').val().length) {
        alert("Please fill out StaffID field!")
        return false;
    }
    if (!$('#Name').val().length) {
        alert("Please fill out Name field!")
        return false;
    }
    if (!$('#Password').val().length) {
        alert("Please fill out Password field!")
        return false;
    }
    if (!$('#Password').val().match(/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}/g)) {
        alert("The password must be 6 characters long, must contain letters (uppercase and lowercase) and digits")
        return false;
    }
    // if(!$('input[name="Position"]:checked').length){
    //     alert("Please select Position!");
    //     return false;
    // }
    if ($('#Position').val() == '-1') {
        alert("Please select Position!");
        return false;
    } else {
        if ($('#Position').val() == 'Area Leader' || $('#Position').val() == 'Leader') {
            var check = 0
            $("input:checkbox[name=Area]:checked").each(function() {
                check++
            });
            if (check == 0) {
                alert("Please select Area");
                return false;
            }
        }
    }

    return true;
}

$('#modalStaff').on('hidden.bs.modal', function() {
    $(this).find('.input').val('');
})

$('#StaffID').on('input', function() {
    showClearInput("#StaffID")
})
$('#Name').on('input', function() {
    showClearInput("#Name")
})
$('#Password').on('input', function() {
    showClearInput("#Password")
})
$('#Title').on('input', function() {
    showClearInput("#Title")
})

$('#clearInputID').on('click', function() {
    clearInput('#clearInputID')
})
$('#clearInputName').on('click', function() {
    clearInput('#clearInputName')
})
$('#clearInputPassword').on('click', function() {
    clearInput('#clearInputPassword')
})
$('#clearInputTitle').on('click', function() {
    clearInput('#clearInputTitle')
})

function showClearInput(input) {
    if ($(input).val().length) {
        $(input).closest("div").find("i").css("display", "block")
    } else {
        $(input).closest("div").find("i").css("display", "none")
    }
}

function clearInput(el) {
    $(el).closest("div").find("input").val('')
    $(el).css('display', 'none')
}

// qr code
$('#tblStaff').on('click', '.btnQrCode', function(e) {
    e.preventDefault()
    var id = $(this).closest('tr').find("#ID").val()
    $.ajax({
        headers: { 'X-CSRF-TOKEN': __csrfToken },
        url: __baseUrl + 'admin/staff/qrCode',
        type: 'POST',
        data: { 'id': id },
        success: function(res) {
            var data = res.data
            $('#StaffIDQR').html(data.StaffID)
            $('#StaffNameQR').html(data.Name)
            $('#imgQR').attr('src', res.file)
            $('#modalQR').modal()
        }
    })
})

$('#btnSaveQR').on('click', function(e) {
    e.preventDefault()

    var src = $('#modalQR').find("#imgQR").attr('src')
    $.ajax({
        headers: { 'X-CSRF-TOKEN': __csrfToken },
        url: __baseUrl + 'admin/staff/saveQR',
        type: 'POST',
        data: { 'src': src },
        success: function(res) {
            if (res.success) {
                $('#modalQR').modal('hide')
                swal({
                    title: 'Saved successfully!',
                    icon: 'success'
                })
                setTimeout(function() {
                    swal.close()
                }, 1500)
            }
        }
    })

})

$('#btnBackQR').on('click', function(e) {
    e.preventDefault()
    var src = $('#modalQR').find("#imgQR").attr('src')
    $.ajax({
        headers: { 'X-CSRF-TOKEN': __csrfToken },
        url: __baseUrl + 'admin/staff/delQR',
        type: 'POST',
        data: { 'src': src },
        success: function(res) {
            if (res.success) {
                $('#modalQR').modal('hide')
            }
        }
    })
})

// id card
$('#tblStaff').on('click', '.btnIdCard', function(e) {
    e.preventDefault()
    var id = $(this).closest('tr').find("#ID").val()
    $.ajax({
        headers: { 'X-CSRF-TOKEN': __csrfToken },
        url: __baseUrl + 'admin/staff/exportNamecard',
        type: 'POST',
        data: { 'id': id },
        success: function(res) {
            window.open(
                res.file,
                '_blank'
            )
        }
    })
})

$('#modalStaff').on('change', '#Position', function() {
    // $("input:checkbox[name=Area]:checked").each(function(){
    //     $(this).prop('checked', false)
    // });
    if ($(this).val() == 'Area Leader' || $(this).val() == 'Leader') {
        $('.form-area').attr('style', 'display:flex !important')
    } else {
        $('.form-area').attr('style', 'display:none !important')
    }
})

$('#serverDataTable th').on('click', function() {
    setSort($(this))
})

// Multiple images preview in browser
var imagesPreview = function(input, placeToInsertImagePreview) {
    swal({ title: "Uploading image", buttons: false, allowOutsideClick: false });
    if (input.files) {
        var filesAmount = input.files.length;
        $(placeToInsertImagePreview).html('');
        for (i = 0; i < filesAmount; i++) {
            /* check ext file */
            var reader = new FileReader();
            var listExtImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tiff', 'bmp'];
            var file = input.files[i].name;
            var dotSeperate = file.lastIndexOf(".") + 1;
            var extFile = file.substr(dotSeperate, file.length).toLowerCase();
            /* reader images */
            reader.onload = function(event) {
                if ($.inArray(extFile, listExtImage) !== -1) {
                    $($.parseHTML('<img>')).attr('src', event.target.result).attr('class', 'img-zoom').attr('style', 'width:100px;').appendTo(placeToInsertImagePreview);
                    $(placeToInsertImagePreview).append('<button class="btn btn-danger btn-clear-image">X</button>')
                    $(placeToInsertImagePreview).append('<input id="changedImage" hidden />')
                } else {
                    $($.parseHTML('<input>')).attr('class', 'files-selected-not-allow').attr('hidden', 'hidden').appendTo(placeToInsertImagePreview);
                }
            }
            reader.readAsDataURL(input.files[i]);
        }
    }
};

function setSort(self) {
    setTimeout(function() {
        var index = $('th:contains(' + self.html() + ')').index()
        var dir = ''
        if (index != 0 && index != 3 && index != 7) {
            var name_col = self.html()
            if (name_col == 'Name') {
                name_col = 'Staff Name'
            }
            $('#currIndexSort').val(index)
            dir = (self.attr('aria-sort') == 'ascending') ? "asc" : "desc"
            $('#currDirSort').val(dir)

            $.ajax({
                url: __baseUrl + 'admin/staff/sessionSort',
                headers: { 'X-CSRF-Token': __csrfToken },
                type: 'post',
                data: { 'col': name_col, 'dir': dir },
            })
        }
    }, 1000)
}

function beforeRender() {
    var col = $('#currIndexSort').val()
    var dir = $('#currDirSort').val()
    table.order([Number(col), dir]).draw()
}
window.onload = beforeRender()