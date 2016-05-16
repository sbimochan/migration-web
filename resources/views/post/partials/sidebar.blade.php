<?php
use App\Nrna\Models\Category;
?>

    <div class="sidebar-wrap col-md-4 col-xs-12 clearfix">
        <div class="row">
            @if(request()->has('category'))
                <?php
                $post_column = $post_column - 2;
                ?>
            <div class="col-md-6 col-xs-12 main-sidebar mCustomScrollbar">

                <?php
                $category = Category::find(request()->get('category'));
                ?>
                <div id="main-menu" class="list-group">
                                <span class="list-group-item min-menu">
                                    <strong>{{$category->title}}</strong>
                                    <a class="pull pull-right"
                                       href="{{route('category.create')}}?section_id={{$category->id}}"><i
                                                class="glyphicon glyphicon-plus add-icon"></i>Add
                                    </a>
                                </span>

                    <div class="list-group-level1 collapse in" aria-expanded="true">
                        <?php
                        $sectionCategories = $category->getimmediateDescendants();
                        $sectionCategories = $sectionCategories->sortBy('position');
                        ?>
                        @foreach($sectionCategories as $child)
                            <?php
                            $url = route('post.index') . "?" . request()->getQueryString();
                            ?>
                            <a href="{{removeParam($url,['sub_category','sub_category1'])}}&sub_category={{$child->id}}"
                               class="list-group-item @if(request()->has('sub_category')&& request()->get('sub_category') == $child->id) active @endif"
                               data-parent="#sub-menu">{{$child->title}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if(request()->has('sub_category'))
                <?php
                $post_column = $post_column - 2;
                $sub_category = Category::find(request()->get('sub_category'));
                ?>
                <div class="col-md-6 col-xs-12 sub-sidebar mCustomScrollbar">
                    <div class="list-group">
                            <span class="list-group-item"><strong>{{$sub_category->title}}</strong><a
                                        class="pull pull-right"
                                        href="{{route('category.create')}}?section_id={{$sub_category->id}}"><i
                                            class="glyphicon glyphicon-plus add-icon"></i>Add</a></span>
                        <?php
                        $subCategories = $sub_category->getImmediateDescendants();
                        $subCategories = $subCategories->sortBy('position');
                        ?>
                        @foreach($subCategories as $child)
                            <a href="{{removeParam($url,'sub_category1')}}&sub_category1={{$child->id}}"
                               class="list-group-item @if(request()->has('sub_category1')&& request()->get('sub_category1') == $child->id) active @endif">{{$child->title}}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
