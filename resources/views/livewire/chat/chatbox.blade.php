
<div>

    @if ($selectedConversation)
        <div class="chatbox_header">

            <div class="return">
                <i class="bi bi-arrow-left"></i>
            </div>

            <div class="img_container">
                <img src="https://ui-avatars.com/api/?name={{ $receiverInstance->name }}" alt="">

            </div>


            <div class="name">
                {{ $receiverInstance->name }}
                @if(\Illuminate\Support\Facades\Cache::has('user-is-online-' . $receiverInstance->id))
            <div class="text-success">Online</div>
        @else
        @isset($receiverInstance->last_seen)
            <div class="text-danger">Last seen {{\Carbon\Carbon::parse($receiverInstance->last_seen)->format('h: i a')}}</div>
            @endisset
                
        
        @endif
            </div>


            <div class="info">

                
               {{---<div class="col-span-1 flex flex-col text-center my-auto">---}}

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button>

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical w-7 h-7 text-gray-700" viewBox="0 0 16 16">
                                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                  </svg>
                           

                            </button>
                        </x-slot>
                        <x-slot name="content">

                            <div class="w-full p-1">

                      
                                <button 
                                onclick="confirm('Are you sure you want to block this person?')||event.stopImmediatePropagation()" wire:click="blockUser('{{ encrypt($selectedConversation->id) }}')"
                               
                                class="items-center gap-3 flex w-full px-4 py-2 text-left text-sm leading-5 text-gray-500 hover:bg-gray-100 transition-all duration-150 ease-in-out focus:outline-none focus:bg-gray-100">

                                    <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-person-fill-slash" viewBox="0 0 16 16">
                     <path d="M13.879 10.414a2.501 2.501 0 0 0-3.465 3.465l3.465-3.465Zm.707.707-3.465 3.465a2.501 2.501 0 0 0 3.465-3.465Zm-4.56-1.096a3.5 3.5 0 1 1 4.949 4.95 3.5 3.5 0 0 1-4.95-4.95ZM11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 8c0 1 1 1 1 1h5.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.544-3.393C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Z"/>
                     </svg>
                                    </span>

                                    Block

                                </button>
                                <button onclick="confirm('Are you sure you want to unblock this user?') || event.stopImmediatePropagation()" wire:click="unblockUser('{{ encrypt($selectedConversation->id) }}')"
                                    class="items-center gap-3 flex w-full px-4 py-2 text-left text-sm leading-5 text-gray-500 hover:bg-gray-100 transition-all duration-150 ease-in-out focus:outline-none focus:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-clipboard2-check" viewBox="0 0 16 16">
                                        <path d="M9.5 0a.5.5 0 0 1 .5.5.5.5 0 0 0 .5.5.5.5 0 0 1 .5.5V2a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 2v-.5a.5.5 0 0 1 .5-.5.5.5 0 0 0 .5-.5.5.5 0 0 1 .5-.5h3Z"/>
                                        <path d="M3 2.5a.5.5 0 0 1 .5-.5H4a.5.5 0 0 0 0-1h-.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1H12a.5.5 0 0 0 0 1h.5a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-12Z"/>
                                        <path d="M10.854 7.854a.5.5 0 0 0-.708-.708L7.5 9.793 6.354 8.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3Z"/>
                                      </svg>
                                    Unblock
                                </button>
                            </div>
                            </x-slot>

                </x-dropdown>
            
           {{---</div>----}}
            </div>
            </div>
        

        <div class="chatbox_body" style="background-image: url('https://media.istockphoto.com/id/1206456618/photo/lightning-during-summer-storm.jpg?s=612x612&w=0&k=20&c=PNxzkL420bVBBF4wM3HlBir3vMbZ0j2AXfEWJKNzn7Q='); background-size: cover; background-repeat: no-repeat; background-position: center center;">
            @foreach ($messages as $message)
                <div class="msg_body  {{ auth()->id() == $message->sender_id ? 'msg_body_me' : 'msg_body_receiver' }}"
                    style="width:80%;max-width:80%;max-width:max-content">

                    {!! html_entity_decode($message->body) !!}
                    <div class="msg_body_footer">
                        <div class="date">
                            {{ $message->created_at->format('h: i a') }}
                        </div>

                        <div class="read">
                            @php
                                
                          if($message->user->id === auth()->id()){

                
                                    if($message->read == 0){


                                        echo'<i class="bi bi-check2 status_tick "></i> ';
                                    }
                                    else {
                                        echo'<i class="bi bi-check2-all text-primary  "></i> ';
                                    }

                          }


                            @endphp
                      

                        </div>
                    </div>
                </div>
            @endforeach

        </div>


        <script>
            $(".chatbox_body").on('scroll', function() {
               
                var top = $('.chatbox_body').scrollTop();
               
                if (top == 0) {

                    window.livewire.emit('loadmore');
                }

            });
        </script>


        <script>
            window.addEventListener('updatedHeight', event => {
             //to adjust the scroll position after calling loadmore
             //based on height difference of chatbox body before and after
                let old = event.detail.height;
                let newHeight = $('.chatbox_body')[0].scrollHeight;

                let height = $('.chatbox_body').scrollTop(newHeight - old);


                window.livewire.emit('updateHeight', {
                    height: height,
                });


            });
        </script>
    @else
        <div class="fs-4 text-center text-primary mt-5">
            no conversation selected
        </div>




    @endif


    <script>
        window.addEventListener('rowChatToBottom', event => {

            $('.chatbox_body').scrollTop($('.chatbox_body')[0].scrollHeight);

        });
    </script>


<script>
    $(document).on('click','.return',function(){


window.livewire.emit('resetComponent');

           if(window.innerWidth>=screen.width*0.85)
           window.location.reload();
}

    );
</script>
 

<script>

window.addEventListener('markMessageAsRead',event=>{
 var value= document.querySelectorAll('.status_tick');

 value.array.forEach(element, index => {
     

    element.classList.remove('bi bi-check2');
    element.classList.add('bi bi-check2-all','text-primary');
 });

});

</script>
</div>
