<div>
   

    @if ($selectedConversation)
        
        <form wire:submit.prevent='sendMessage' action=""  enctype="multipart/form-data">
            <div class="chatbox_footer">
            <div class="custom_form_group">
        
               
                <textarea wire:model='body' id="sendMessage" class="control" placeholder="Write message" rows="1" cols="1"></textarea>
                <label for="sendFile" class="custom_file_button">
                   
                    
                    <pre>Choose<br>File</pre>
                </label>

                <input wire:model='file' type="file" id="sendFile" style="display: none">
           
            <button type="submit" class="submit">Send</button>
            @if ($errorMessage)
            <div class="text-danger" id="error">{{ $errorMessage }}</div>
        @endif
            </div>
        
            </div>
        </form>

    @endif

</div>
