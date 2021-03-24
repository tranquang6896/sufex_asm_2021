$(document).ready(function(){
    $('.report-jp').on('click', function(){
        translateReport('JP')
    })
    $('.report-en').on('click', function(){
        translateReport('EN')
    })
    $('.report-vn').on('click', function(){
        translateReport('VN')
    })
})

function translateReport(lang){
    // type
    var type = "#type" + lang
    $('.type-report').html($(type).val())

    // content
    var content = "#report" + lang
    $('.textarea-report').val($(content).val())
}
