<div class="form-group {{ $errors->has('audio') ? 'has-error' : ''}}">
    {!! Form::label('audio', 'Audio: ', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('metadata[data][audio]', ['class'=>'form-control' , 'id' => 'file'])!!}
        {!! $errors->first('audio', '<p class="help-block">:message</p>') !!}
    </div>
    @if(isset($post->metadata->data->audio ))
        <div><a target="_blank" href="{{$post->metadataWithPath->data->audio}}">check</a></div>
    @endif
</div>
<div class="form-group {{ $errors->has('thumbnail') ? 'has-error' : ''}}">
    {!! Form::label('thumbnail', 'Audio Image: ', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::file('metadata[data][thumbnail]', ['class'=>'form-control' , 'id' => 'thumbnail'])!!}
        {!! $errors->first('thumbnail', '<p class="help-block">:message</p>') !!}
    </div>
    @if(isset($post->metadata->data->thumbnail ))
        <div><a target="_blank" href="{{$post->metadataWithPath->data->thumbnail}}">check</a></div>
    @endif
</div>
<hr>