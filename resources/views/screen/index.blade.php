@extends('layouts.master')

@section('content')
	@include('block.navbar')
	<div>
		<h3>Dynamic Screens <a href="{{ route('screen.create') }}" class="btn btn-primary pull-right btn-sm">Create New Screen</a></h3>
		<div>
			<table class="table table-bordered table-striped table-hover">
				<thead>
				<tr>
					<th></th>
					<th>Icon</th>
					<th>Name</th>
					<th>Title</th>
					<th>Type</th>
					<th>Visibility</th>
					<th>Actions</th>
				</tr>
				</thead>
				<tbody class="sortable" data-entityname="screen">
				@forelse($screens as $screen)
					<tr data-itemId="{{ $screen->id }}">
						<td class="sortable-handle"><span class="glyphicon glyphicon-sort"></span></td>
						<td class="sortable-handle"><img height="70px" width="100px" src="{{ $screen->icon_image_path
						}}"/></td>
						<td class="sortable-handle"><a href="{{ route('screen.edit', [$screen->id]) }}">{{ $screen->name }}</a></td>
						<td class="sortable-handle">{{ $screen->title }} <span class="label
						label-default">{{$screen->state}}</span></td>
						<td class="sortable-handle">{{ $screen->type }}</td>
						<td class="sortable-handle">
							@if(!is_null($screen->visibility) && isset($screen->visibility['country_id']))
								@foreach($screen->visibility['country_id'] as $country)
									@if($country=='all')
										<span class="label label-default">All country</span>
									@else
										<?php $countryObject = \App\Nrna\Models\Category::find($country);?>
										<span class="label label-default">{{$countryObject->title}}</span>
									@endif

								@endforeach
							@endif
							<br>
							@if(!empty($screen->visibility['gender']))
								<span class="label label-default">
									{{config('screen.gender.'.$screen->visibility['gender'])}}
								</span>
							@endif
						</td>

						<td>
							<a href="{{ route('screen.edit', $screen->id) }}">
								<button type="submit" class="btn btn-primary btn-xs table-button">Update</button>
							</a> /
							@if($screen->type=="block")
								<a href="{{ route('blocks.index', ["page"=>'dynamic',"screen_id"=>$screen->id]) }}">
									<button type="button" class="btn btn-primary btn-xs table-button">Manage</button>
								</a>
							@endif
							@if($screen->type=="feed")
								<a href="{{ route('screen.feed.create', $screen->id) }}">
									<button type="button" class="btn btn-primary btn-xs table-button">Manage</button>
								</a>
							@endif /
							{!! Form::open([
								'method'=>'DELETE',
								'route' => ['screen.destroy', $screen->id],
								'style' => 'display:inline'
							]) !!}
							{!! Form::submit('Remove', ['class' => 'btn btn-danger btn-xs table-button']) !!}
							{!! Form::close() !!}
						</td>
					</tr>
				@empty
					<tr>
						<td>No Record Found</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>
	</div>
@endsection

@section('script')
	<script src="{{asset('js/jquery-ui-1.10.4.custom.min.js')}}"></script>
	<script src="{{asset('js/sort.js')}}"></script>
@endsection
