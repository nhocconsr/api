@extends('layouts.admin')

@section('content')
@php( $categories_item = isset($categories_item) ? $categories_item : false)

<form class="form-horizontal" role="form" method="POST" action="{!! URL::route('image.saveCat')!!}">

    <input  type="hidden" name='id' value="{{ $categories_item ? $categories_item->id : '' }}">

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{ $categories_item ? "Edit" : 'Create' }} Categories</h2>
            
            <ol class="breadcrumb">
                <li>
                    <a href="{{url('/admin')}}">Home</a>
                </li>
                <li>
                    <a href="{!! URL::route('image.cats')!!}">Categories</a>
                </li>
                <li class="active">
                    <strong>{{ $categories_item ? "Edit" : 'Create' }} Categories</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            <br>
            <br>
            <div class="pull-right tooltip-demo">
                <button  class="btn btn-sm btn-primary dim" data-toggle="tooltip" data-placement="top" title="Add new Categories"><i class="fa fa-plus"></i> Save</button>
                @if($categories_item)
                <a href="{!! URL::route('image.createCat')!!}" type="button" class="btn btn-info btn-dm dim">New</a>
                @endif
                <a href="{{ URL::previous() }}" class="btn btn-danger btn-sm dim" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cancel Edit"><i class="fa fa-times"></i>Discard</a>
            </div>
        </div>
    </div>
    

    {{ csrf_field() }}
    <!--input type="hidden" name="id" value="{{empty($user) ? old('id') : $user->id}}" /-->
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">                
                <div class="ibox-content">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">     
                            Name
                        </label>
                        <div class="col-sm-10">
	                    	<input class="form-control" type="text" name='name' value="{{old('name') ? old('name') : ($categories_item ? $categories_item->name : '')}}">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>

                

                    <div class="form-group">
                        <label class="col-sm-2 control-label">   
                            Description
                        </label>
                        <div class="col-sm-10">
	                    	<input class="form-control" type="text" name='description' value="{{old('description') ? old('description') : ($categories_item ? $categories_item->description : '') }}">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>     

                    <div class="form-group">
                        <label class="col-sm-2 control-label">   
                            Parent_id
                        </label>
                        <div class="col-sm-10">
                            <select name="parent_id" class="form-control chosen-select" >
                                <option value="0">none</option>                                     
                                @foreach ($categories_level as $categories_level_1)		
                                    @if (!$categories_item)	
										<option value="{{$categories_level_1->id}}">{{$categories_level_1->name}}</option>
                                    @else
                                        @if ($categories_level_1->id == $categories_item->parent_id)        
                                            <option value="{{$categories_level_1->id}}" selected="selected">{{$categories_level_1->name}}</option>
                                        @else  
                                        <option value="{{$categories_level_1->id}}">{{$categories_level_1->name}}</option>  
                                        @endif   
                                    @endif
									@foreach ($categories as $categories_level_2)
										@if ($categories_level_2->parent_id == $categories_level_1->id)
                                            @if (!$categories_item) 
											    <option value="{{$categories_level_2->id}}">&nbsp;&nbsp;&nbsp;{{$categories_level_2->name}}</option>
                                            @else
                                                @if ($categories_level_2->id == $categories_item->parent_id)    
                                                    <option value="{{$categories_level_2->id}}" selected="selected">&nbsp;&nbsp;&nbsp;{{$categories_level_2->name}}</option>
                                                @else       
                                                    <option value="{{$categories_level_2->id}}">&nbsp;&nbsp;&nbsp;{{$categories_level_2->name}}</option>
                                                @endif
                                            @endif
											@foreach ($categories as $categories_level_3)
												@if ($categories_level_3->parent_id == $categories_level_2->id)
                                                    @if (!$categories_item) 
														<option value="{{$categories_level_3->id}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$categories_level_3->name}}</option>
                                                    @else
                                                        @if ($categories_level_3->id == $categories_item->parent_id)        
                                                            <option value="{{$categories_level_3->id}}" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$categories_level_3->name}}</option>
                                                        @else       
                                                            <option value="{{$categories_level_3->id}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$categories_level_3->name}}</option>
                                                        @endif
                                                    @endif
												@endif
											@endforeach
										@endif
									@endforeach			
								@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>     

                    <div class="form-group">
                        <label class="col-sm-2 control-label">   
                            Published
                        </label>
                        <div class="col-sm-10">
                            <input class="js-switch" value="1" style="display: none;" data-switchery="true" type="checkbox" name="published" {{(old('published') || $categories_item == false || ($categories_item && $categories_item->published)) ? 'checked' : '' }} >
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>    
                    
                      
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@section("content_js")
<script src="{!! asset('assets/js/plugins/chosen/chosen.jquery.js') !!}"></script>
<link href="{!! asset('assets/css/plugins/chosen/chosen.css')!!}" rel="stylesheet">
<script>
    
    var elem = document.querySelector('.js-switch');
    var switchery = new Switchery(elem, {color: '#1AB394'});
    
      var config = {
                '.chosen-select'           : {},
                '.chosen-select-deselect'  : {allow_single_deselect:true},
                '.chosen-select-no-single' : {disable_search_threshold:10},
                '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
                '.chosen-select-width'     : {width:"95%"}
                }
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
</script>
@endsection