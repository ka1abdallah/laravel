<div>
  
<ul class="list-group w-75 mx-auto mt-3 container-fluid" style="height: 1000px">
{{---rounded corners,75% of parent width container,centering,top margin,height.stretch--}}
@foreach ($users as $user)


    <li class="list-group-item{{--padding,border,bgcolor---}} 
              list-group-item-action"{{---cursor pointer,highlighting effect----}}
        wire:click='checkconversation({{$user->id}})'> {{$user->name}}</li>


@endforeach
 </ul>

</div>
