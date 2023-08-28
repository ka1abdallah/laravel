<?php

namespace App\Http\Livewire\Chat;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Php;
use Livewire\WithFileUploads;

class SendMessage extends Component
{    use WithFileUploads;
    public $selectedConversation;
    public $receiverInstance;
    public $body;
    public $file;
    public $createdMessage;
    protected $listeners = ['updateSendMessage', 'dispatchMessageSent','resetComponent'];


    public function resetComponent()
    {
   
  $this->selectedConversation= null;
  $this->receiverInstance= null;
 
       
    }
  

    
    function updateSendMessage(Conversation $conversation, User $receiver)
    {

       
        $this->selectedConversation = $conversation;
        $this->receiverInstance = $receiver;
       
    }

    public $errorMessage;
    
    public function sendMessage()
{
    if ($this->file) {
        // If a file is selected, create a message with the file
        // and return immediately after sending the file.
        $this->sendMessageWithFile();
        return;
    }
    if($this->body){ 
    // If no file is selected, proceed with sending a regular text message.
    $this->sendMessageWithText();
    return;
}
}

    public function sendMessageWithText()
    {
        if ($this->body == null && $this->file == null) {
            return null;
        }
           // Get the receiver of the message
           $receiver = $this->selectedConversation->getReceiver();
           $blockedUserIdsrec = json_decode($receiver->blocked_users_id, true) ?? [];
           $blockedUserIdssen = json_decode(auth()->user()->blocked_users_id, true) ?? [];
       
           if (in_array(auth()->user()->id, $blockedUserIdsrec)) {
          // If the receiver's ID is in the blocked_users_id array of the authenticated user,
          // it means the receiver has blocked the sending user. So, we cannot send the message.
          // You can handle this situation here, for example, by displaying an error message.
          $this->errorMessage = "You cannot send a message, this user blocked you";
          return ;}
          else if(in_array($receiver->id,$blockedUserIdssen)){ 
          $this->errorMessage = "Unblock this user to send him a message";
          return ;}
      
      $this->errorMessage = "";
        $this->createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiverInstance->id,
            'body' => $this->body,

        ]);

        $this->selectedConversation->last_time_message = $this->createdMessage->created_at;
        $this->selectedConversation->save();
        $this->emitTo('chat.chatbox', 'pushMessage', $this->createdMessage->id);


        //reshresh coversation list 
        $this->emitTo('chat.chat-list', 'refresh');
        $this->reset('body');

        $this->emitSelf('dispatchMessageSent');
        
    }

    public function sendMessageWithFile()
{
    if ($this->file) {
         // Get the receiver of the message
         $receiver = $this->selectedConversation->getReceiver();
         $blockedUserIdsrec = json_decode($receiver->blocked_users_id, true) ?? [];
         $blockedUserIdssen = json_decode(auth()->user()->blocked_users_id, true) ?? [];
     
         if (in_array(auth()->user()->id, $blockedUserIdsrec)) {
        // If the receiver's ID is in the blocked_users_id array of the authenticated user,
        // it means the receiver has blocked the sending user. So, we cannot send the message.
        // You can handle this situation here, for example, by displaying an error message.
        $this->errorMessage = "You cannot send a message, this user blocked you";
        return ;}
        else if(in_array($receiver->id,$blockedUserIdssen)){ 
        $this->errorMessage = "Unblock this user to send him a message";
        return ;}
        $this->errorMessage = "";
        // Handle the file upload and save it on the server's file system.
        $uploadedFile = $this->file->store(''); // This stores the file in the 'files' directory.
         // Set the body to the link of the uploaded file
        
         $fileUrl = asset('storage/' . $uploadedFile);
        // Create the message with the file path.
          // Get the name of the file from the path
          $filename = basename($this->file->getClientOriginalName());
        $this->createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiverInstance->id,
            'file_path' => $uploadedFile,
            'body' =>"You received a file. Click here to download: <a href='" . $fileUrl . "' target='_blank' style='color: blue;'>".$filename."</a>", // Save the file path in the 'file_path' column.
        ]);
        $this->selectedConversation->last_time_message = $this->createdMessage->created_at;
        $this->selectedConversation->save();
        $this->emitTo('chat.chatbox', 'pushMessage', $this->createdMessage->id);
        //refresh chatlist
        $this->emitTo('chat.chat-list', 'refresh');
        // Reset the text and file inputs.
        $this->reset(['body', 'file']);

        // Broadcast the message sent event.
        $this->emitSelf('dispatchMessageSent');
    }
}


    public function dispatchMessageSent()
    {

        broadcast(new MessageSent(Auth()->user(), $this->createdMessage, $this->selectedConversation, $this->receiverInstance));
        
    }
    public function render()
    {
        return view('livewire.chat.send-message');
    }
}
