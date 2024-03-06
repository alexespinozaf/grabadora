<audio id="{{$recording->id}}" controls>
    <source src="{{url('/storage/').$recording->file}}" type="audio/mpeg">
</audio>
<button data-id="{{$recording->id}}" id="deleteRecording" class="btn btn-danger"
        onclick="return confirm('Esta seguro que quiere descartar este audio?')?deleteRecording():'';"
        style="margin-left: 10px;">Descartar
</button>
<br>
<p style="margin-left: 10px; margin-top: auto">{{$recording->updated_at}}</p>

