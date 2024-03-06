@extends('layouts.gradesTemplate')

@section('css')
<script type="text/javascript" src="{{ URL::asset('js/clases/service.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
    @if($activity !== null)
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Activity Evaluation
                        </div>
                        <div class="card-body">
                            <div class="form-row mb-5">
                                <div class="col-md-8 ">
                                    <div class="align-items-end">
                                        <label for="groups">Groups:</label>
                                        <input type="hidden" name="resourcelink_id" id="resourcelink_id" value="{{ $instance['resourceLink']['id'] }}">
                                        <input type="hidden" name="resourcelink_id" id="platform_id" value="{{ $instance['platform']['id'] }}">
                                        <select id="groups" name="groups" class="form-control custom-select">
                                            <option value="">Select group</option>
                                            @foreach($groups as $group)
                                                <option value="{{ $group['id'] }}">{{ $group['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-group w-100 text-right" style="margin-top: 25px">
                                        <button id="publishGrades" class="btn btn-outline-success" data-toggle="modal" data-target="#evaluationModal" disabled>Publish Grades</button>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered table-hover" id="studentsTable" style="display:none;">
                                <thead>
                                <tr>
                                    <th class="" width="1%">Acción</th>
                                    <th class="" width="10%">Id</th>
                                    <th class="" width="10%">Name</th>
                                    <th class="" width="10%">Email</th>
                                    <th class="" width="20%">Recording</th>
                                    <th class="" width="10%">Recording Date</th>
                                    <th class="" width="27%">Grade</th>
                                    <th class="" width="22%">Comment</th>
                                    <th class="" width="10%">Grade Date</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div id="toast-container" class="toast-container position-absolute top-0 start-50 translate-middle-x"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="evaluationModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header" style="height:50px;">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                    <div class="modal-body">
                        <div class="">
                            <input type="hidden" name="studentId" id="studentId">
                            <input type="hidden" name="instance" id="instance" value="{{ json_encode($instance)}}" >
                            <input type="hidden" name="content" id="content" value="{{ json_encode($content)}}" >
                            <table class="table table-bordered table-hover" id="studentsTable">
                                <thead>
                                    <tr>
                                        <th class="" width="20%">N° Of students with grades</th>
                                        <th class="" width="20%">N° Of studens without grades</th>
                                        <th class="" width="20%">Total Students</th>
                                        <th class="" width="20%">End Date</th>
                                        <th class="" width="20%">Remaining time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="countGrades" class="" width="20%"></td>
                                        <td id="difference" class="" width="20%"></td>
                                        <td id="countAll" class="" width="20%"></td>
                                        <td id="endDate" class="" width="20%"></td>
                                        <td id="remainingTime" class="" width="20%"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        @if($activity->end_date >= date('Y-m-d H:i:s'))
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" data-placement="top" title="It's not yet the date to publish">
                                <button type="button" class="btn btn-outline-primary" id="publishGrades" disabled>Publish </button>
                            </span>
                        @else
                            <button id="publishConfirmation" type="button" class="btn btn-outline-primary" value="Publish" name="guardar">Publish</button>
                        @endif
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                    </div>
              </div>

            </div>
        </div>
    @else
        <div class="container">
            <div class="row justify-content-center">
                <div class="alert alert-warning text-center">
                    <h1>Activity not available</h1>
                    <div class="h5">No linked audio</div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('js')
<script>
    //SE DEFINE LA CLASE PARA LAS SOLICITUDES DEL SERVICIO.
    const gradeService = new GradeService();


    $(function() {
        //AJUSTA EL TAMAÑO DEL SELECTOR
        $("#groups").select2({
            width: '100%'
        });
        //FUNCION CAPTURAR EL CAMBIO DE SELECCION DE GRUPO
        $("#groups").change(function() {
            var idGroup = $(this).val();
            var table = $("#studentsTable tbody");
            if (idGroup) {
                $('#publishGrades').removeAttr('disabled');
                $('#studentsTable').show();
                cargarDatatable();
            }
        });
        //FUNCIÓN PARA CARGAR EL DATATABLE
        function cargarDatatable() {
            var idGroup = $("#groups").val();
            var resourcelink_id = $("#resourcelink_id").val();
            var platform_id = $("#platform_id").val();
            //SE DEFINE Y CONFIGURA EL DATATABLE.
            $('#studentsTable').DataTable({
                "info": false,
                "processing": false,
                "deferRender": true,
                "responsive": true,
                "destroy": true,
                "scrollX": true,
                "ajax": {
                    url: "{{ url('members') }}",
                    type: "POST",
                    data: {
                        idGroup: idGroup,
                        resourcelink_id: resourcelink_id,
                        platform_id: platform_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                    }
                },
                "columns": [{
                    //     data: "number",
                    //     searchable: true,
                    //     orderable: true,
                    //     className: "text-center"
                    // },
                        data: "recording",
                        className: "text-center", render: function (data, type, row) {
                            let recording = row.recording;
                            if(recording){
                                return '<button type="button" class="btn btn-outline-danger" onclick="return confirm('+"'Are you sure you want to discard this recording?'"+')?deleteRecording('+row.recording.id+'):'+"''"+';">Delete</button>';

                            }
                            else {
                                return '<a>No action</a>'
                            }
                        }
                     },
                    {
                        data: "id",
                        searchable: false,
                        orderable: false,
                        visible: false,
                        className: "text-center"
                    },
                    {
                        data: "name",
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: "email",
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: "recording",
                        className: "text-center", render: function (data, type, row) {
                            var recording = row.recording;
                            if(recording){
                                return '<audio id="' + row.recording.id + '" controls> <source src="{{url('/storage/')}}'+row.recording.file+'" type="audio/mpeg"> </audio>'
                            } else {
                                return '<a>No recording</a>'
                            }
                        }
                    },
                    {
                        data: "recording_date",
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    },
                    {
                        data: "grade",
                        searchable: true,
                        orderable: true,
                        className: "text-center",
                        render: function(data, type, row, meta) {
                            if(row.recording){
                                if (row.grade != 0) {
                                    return '<input id="' + row.id + '" data-input="grade" name="grade" min="1.0" max="7.0" type="number" step="0.1" class="form-control text-center datatable_input numbersOnly border-success" ' +
                                        'value="' + parseFloat(row.grade) + '" onkeypress="return isNumberKey(event);" onfocus="clear()"/>';
                                }
                                else {
                                    return '<input id="' + row.id + '" data-input="grade" name="grade" min="1.0" max="7.0" type="number" step="0.1" class="form-control text-center datatable_input numbersOnly" ' +
                                        'value="' + parseFloat(row.grade) + '" onkeypress="return isNumberKey(event);" onfocus="clear()"/>';

                                }
                            } else {
                                return '<input id="' + row.id + '" data-input="grade" name="grade" min="1.0" max="7.0" type="number" step="0.1" class="form-control text-center datatable_input numbersOnly" ' +
                                    'value="" onkeypress="return isNumberKey(event);" onfocus="clear()" disabled/>';
                            }
                        }
                    },
                    {
                        data: "comment",
                        searchable: true,
                        orderable: true,
                        className: "text-center",
                        render: function(data, type, row, meta) {
                            if(row.recording){
                                if (row.grade != 0) {
                                    return '<textarea id="' + row.id + '" data-input="comment" name="comment" type="text-area" class="form-control datatable_input border-success">'+row.comment+'</textarea>';
                                }
                                else {
                                    return '<textarea id="' + row.id + '" data-input="comment" name="comment" type="text-area" class="form-control datatable_input">'+row.comment+'</textarea>';

                                }
                            } else {
                                return '<textarea id="' + row.id + '" data-input="comment" name="comment" type="text-area" class="form-control datatable_input" disabled></textarea>';
                            }
                        }
                    },
                    {
                        data: "grade_date",
                        searchable: true,
                        orderable: true,
                        className: "text-center"
                    }
                ],
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Todo"]
                ],
                "language": {
                    "emptyTable": "No hay información de este grupo",
                    "search": "Buscar:",
                    "paginate": {
                        "first":      "Primero",
                        "last":       "Ultimo",
                        "next":       "Siguiente",
                        "previous":   "Anterior"
                    },
                    "lengthMenu": "Mostrar _MENU_ entradas",
                    "loadingRecords": "Cargando...",
                }

            }).on('preXhr.dt', function() {

            });

        }
        //FUNCIÓN PARA RELLENAR LOS VALORES DEL MODAL
        //ajax function to get data from route countGrades
        $('#publishGrades').on("click", function() {
            var idGroup = $("#groups").val();
            var resourcelink_id = $("#resourcelink_id").val();
            var platform_id = $("#platform_id").val();
            var instance = $('#instance').val();
            var content = $('#content').val();
            $.ajax({
                url: "{{ url('countGrades') }}",
                type: "POST",
                data: {
                    idGroup: idGroup,
                    resourcelink_id: resourcelink_id,
                    platform_id: platform_id,
                    instance: instance,
                    content: content,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    $('#countGrades').text(data.countGrades);
                    $('#countAll').text(data.countAll);
                    $('#difference').text(data.difference);
                    $('#remainingTime').text(data.remainingTime+' Days');
                    $('#endDate').text(data.endDate);
                    console.log('now: '+data.dateNow, 'end: '+data.endDate, 'remaining: '+data.remainingTime)
                }
            });
        });


        //FUNCION PARA PUBLICAR LAS NOTAS EN MOODLE
        $('#publishConfirmation').on("click", async function() {
            var instance = $('#instance').val();
            var content = $('#content').val();
            var groupId = $("#groups").val();
            var token = $('meta[name="csrf-token"]').attr('content');
            var spinner = document.createElement('span');
            this.disabled = true;
            spinner.setAttribute('class',"spinner-border spinner-border-sm");
            this.appendChild(spinner);
            try {
                const response = await gradeService.publishGrades(instance,content,groupId, token);
                console.log(response)

                if(response.status == 200){
                    this.disabled = false;
                    this.removeChild(spinner);
                    $('#success-div').css("text-align", "center");
                    $("#success-div").html(response.message).fadeIn('slow');
                    $('#success-div').delay(3000).fadeOut('slow');
                }else {
                    this.disabled = false;
                    this.removeChild(spinner);
                    $('#error-div').css("text-align", "center");
                    $("#error-div").html(response.message).fadeIn('slow');
                    $('#error-div').delay(3000).fadeOut('slow');
                }
            }
            catch (error){
                this.disabled = false;
                this.removeChild(spinner);
                $("#error-div").html(error).fadeIn('slow');
                $('#error-div').delay(3000).fadeOut('slow');
            }
            $('#evaluationModal').modal('hide');
        });

        //FUNCION PARA DESBANECER ALERTA
        setTimeout(function(){ $('.alert-success').fadeOut() }, 3000);

        //FUNCION PARA LIMPIAR EL INPUT DE LA NOTA
        $('#studentsTable').on('focus', 'input', function() {
            if ($(this).val() == 0) {
                $(this).val('');
            }
        });
        //FUNCION PARA CARGAR NOTAS Y COMENTARIOS A LA GRABADORA
        $('#studentsTable').on('blur', '.datatable_input' , async function(e){
            var grade = '';
            var comment = '';
            const row = $(this).closest("tr");
            const prevtr = row.prev('tr')[0];
            var activity = $("#resourcelink_id").val();
            var token = $('meta[name="csrf-token"]').attr('content');
            var idGroup = $("#groups").val();
            console.log(idGroup);
            //VERIFICA SI LA FILA ES CHILD.
            if (row.hasClass('child')) {
                //SI LO ES, ENTONCES APUNTA A LA FILA ANTERIOR.
                var studentId = $('#studentsTable').DataTable().row(prevtr).data()['id'];
                var recordingId = $('#studentsTable').DataTable().row(prevtr).data()['recording']['id'];
            }
            else {
                var studentId = $('#studentsTable').DataTable().row(row).data()['id'];
                var recordingId = $('#studentsTable').DataTable().row(row).data()['recording']['id'];
            }
            //CONSULTA SI ES GRADE O COMMENT
            if($(this).attr('data-input') == "grade") {
                 grade = $(this).val();
            } else {
                 comment = $(this).val();
            }
            try {
                const response = await gradeService.storeGrade(grade, comment, studentId, activity, recordingId, token, idGroup);
                if(response.status == 200){
                    $(this).removeClass('is-invalid');
                    $(this).addClass('is-valid');
                    $(this).tooltip("disable").tooltip("hide");
                    if (row.hasClass('child')) {
                        $('#studentsTable').DataTable().row(prevtr).data().grade_date = response.grade_date;
                    }
                    else {
                        $('#studentsTable').DataTable().row(row).data().grade_date = response.grade_date;

                    }
                    console.log($('#studentsTable').DataTable().row(row).data().grade_date)
                    $('#studentsTable').DataTable().ajax.reload();
                    setTimeout(function () {
                        $(this).removeClass('is-valid');
                    }, 3000);
                }else {
                    console.log("aqui")
                    $(this).addClass('is-invalid');
                    $(this).tooltip({'trigger': 'hover manual', 'placement': 'bottom', 'title': response.message}).tooltip("enable").tooltip('show');
                }
            }
            catch (error) {
                if (!$(this).val() && $(this).attr('data-input') == "grade") {
                    $(this).val(0);
                }
            }
        });
    });

    function isNumberKey(event) {
        if ((event.which != 46 || event.target.value.indexOf('.') != -1) &&
            ((event.which < 48 || event.which > 57) && (event.which != 0 && event.which != 8))) {
            event.preventDefault();
        }
        // var text = event.target.value;
        // if ((text.indexOf('.') != -1) &&
        // 	(text.substring(text.indexOf('.')).length > 6) &&
        // 	(event.which != 0 && event.which != 8) &&
        // 	(event.target.selectionStart >= text.length - 6)) {
        // 		event.preventDefault();
        // }
    }
    function deleteRecording(recordingId){
        let token = $("meta[name='csrf-token']").attr("content");
        $.ajax(
            {
                url: window.location.origin + '/recorder/' + recordingId,
                type: 'DELETE',
                data: {
                    "id": recordingId,
                    "_token": token,
                },
                success: function (response) {
                    if(response.status === 200){
                        //boostrap toaster success message
                        createToastSuccess(response.message);
                        $('#studentsTable').DataTable().ajax.reload();
                        //M.toast({html: response.message, duration: 18000, classes: 'rounded green darken-1'});
                    } else {
                        createToastError(response.message);
                        $('#studentsTable').DataTable().ajax.reload();
                    }
                },
                error: function (qxhr, status, error) {
                    console.log(qxhr)
                    createToastError(error);
                }
            });
    }

    function createToastSuccess(message) {
        const toast = document.createElement('div');
        toast.classList.add('toast');
        toast.classList.add('show');
        toast.classList.add('bg-success');
        toast.classList.add('text-white');
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.setAttribute('data-delay', '5000');
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="mr-auto">Success</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        console.log(toast);
        document.getElementById('toast-container').appendChild(toast);
        $('.toast').toast('show');
    }

    function createToastError(message) {
        const toast = document.createElement('div');
        toast.classList.add('toast');
        toast.classList.add('show');
        toast.classList.add('bg-danger');
        toast.classList.add('text-white');
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.setAttribute('data-delay', '5000');
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="mr-auto">Error</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        console.log(toast);
        document.getElementById('toast-container').appendChild(toast);
        $('.toast').toast('show');
    }

</script>
@endsection
