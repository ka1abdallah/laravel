<?php

namespace App\Http\Livewire\Chat;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;




class Chatbox extends Component
{

    public $selectedConversation;
    public $receiver;
    public $messages;
    public $paginateVar = 10;
    public $height;
    
    public function getReceiver()
    {

        if ($this->sender_id === auth()->id()) {

            return User::firstWhere('id',$this->receiver_id);

        } else {

            return User::firstWhere('id',$this->sender_id);
        }
    }

   
    public function blockUser($id)
    {
        
        $conversation = Conversation::find(decrypt($id));
       
        
        $userToBlock = $conversation->getReceiver();
       
        // Check if the authenticated user is already blocked by the user to be blocked
        $blockedUsersIds = json_decode(auth()->user()->blocked_users_id, true) ?? [];
        if (in_array($userToBlock->id, $blockedUsersIds)) {
            // If the authenticated user is already blocked, do not proceed with blocking.
            
            return;
        }
    
        
    
        // Add the new blocked user ID to the array
        $blockedUsersIds[] = $userToBlock->id;
    
        // Update the blocked_users_id field of the authenticated user with the updated JSON data
        auth()->user()->blocked_users_id = json_encode($blockedUsersIds);
        auth()->user()->save();
    
        // After blocking, refresh the chat box
        $this->emit('refresh');
    }
    public function unblockUser($id)
    {
        // Find the conversation
        $conversation = Conversation::find(decrypt($id));
    
        // Get the user to be unblocked
        $userToUnblock = $conversation->getReceiver();
    
        // Get the blocked users IDs of the authenticated user
        $blockedUsersIds = json_decode(auth()->user()->blocked_users_id, true) ?? [];
    
        // Check if the userToUnblock's ID exists in the blocked_users_id array
        $userIndex = array_search($userToUnblock->id, $blockedUsersIds);
        if ($userIndex !== false) {
            // If the user is blocked, remove their ID from the array
            unset($blockedUsersIds[$userIndex]);
    
            // Update the blocked_users_id field of the authenticated user with the updated JSON data
            auth()->user()->blocked_users_id = json_encode(array_values($blockedUsersIds));
            auth()->user()->save();
    
            // After unblocking, refresh the chat list
            $this->emit('refresh');
        }
    }
   
    public function  getListeners()
    {

        $auth_id = auth()->user()->id;
        return [
            "echo-private:chat.{$auth_id},MessageSent" => 'broadcastedMessageReceived',
            "echo-private:chat.{$auth_id},MessageRead" => 'broadcastedMessageRead',
            'loadConversation', 'pushMessage', 'loadmore', 'updateHeight','broadcastMessageRead','resetComponent'
        ];
    }



    public function resetComponent()
  {
 
$this->selectedConversation= null;
$this->receiverInstance= null;
    
  }

    public function broadcastedMessageRead($event)
    {
       

        if($this->selectedConversation){



           // if((int) $this->selectedConversation->id === (int) $event['conversation_id']){

                $this->dispatchBrowserEvent('markMessageAsRead');
           // }

        }

        
    }
    /*---------------------------------------------------------------------------------------*/
    /*-----------------------------Broadcasted Event fucntion-------------------------------------------*/
    /*----------------------------------------------------------------------------*/

    function broadcastedMessageReceived($event)
    {
        
      $this->emitTo('chat.chat-list','refresh');
        
        
        $broadcastedMessage = Message::find($event['message']);


        #check if any selected conversation is set 
        if ($this->selectedConversation) {
            #check if Auth/current selected conversation is same as broadcasted selecetedConversationgfg
            if ((int) $this->selectedConversation->id  === (int)$event['conversation_id']) {
                # if true  mark message as read
                $broadcastedMessage->read = 1;
                $broadcastedMessage->save();
                $this->pushMessage($broadcastedMessage->id);
                

                $this->emitSelf('broadcastMessageRead');
               
            }
        }
    }


    public function broadcastMessageRead( )
    {
        broadcast(new MessageRead($this->selectedConversation->id, $this->receiverInstance->id));
        
    }

    /*--------------------------------------------------*/
    /*------------------push message to chat--------------*/
    /*------------------------------------------------ */
    public function pushMessage($messageId)
    {
        $newMessage = Message::find($messageId);
       
        $this->messages->push($newMessage);
        $this->dispatchBrowserEvent('rowChatToBottom');

    }
    /*--------------------------------------------------*/
    /*------------------load More --------------------*/
    /*------------------------------------------------ */
    function loadmore()
    {//same logic as loadconversation but increase paginatevar and update height

        
        $this->paginateVar = $this->paginateVar + 10;
        $this->messages_count = Message::where('conversation_id', $this->selectedConversation->id)->count();

        $this->messages = Message::where('conversation_id',  $this->selectedConversation->id)
            ->skip($this->messages_count -  $this->paginateVar)
            ->take($this->paginateVar)->get();

        //$height = $this->height;
        $this->dispatchBrowserEvent('updatedHeight', ($this->height));
        
    }


    /*---------------------------------------------------------------------*/
    /*------------------Update height of messageBody-----------------------*/
    /*---------------------------------------------------------------------*/
    function updateHeight($height)
    {

       
        $this->height = $height;

       
    }



    /*---------------------------------------------------------------------*/
    /*------------------load conersation----------------------------------*/
    /*---------------------------------------------------------------------*/
    public function loadConversation(Conversation $conversation, User $receiver)
    {
        $this->selectedConversation =  $conversation;
        $this->receiverInstance =  $receiver;
        // Count the total number of messages in the conversation
        $this->messages_count = Message::where('conversation_id', $this->selectedConversation->id)->count();
        //get all messages related to the conversation,and load only the most recent
        //messages according to the paginatevar value
        $this->messages = Message::where('conversation_id',  $this->selectedConversation->id)
            ->skip($this->messages_count -  $this->paginateVar)
            ->take($this->paginateVar)->get();
        //trigger chat selected event
        $this->dispatchBrowserEvent('chatSelected');
        //update the read status to 1 of all messages in selected conversation
        Message::where('conversation_id',$this->selectedConversation->id)
         ->where('receiver_id',auth()->user()->id)->update(['read'=> 1]);
        //broadcast message readevent
        $this->emitSelf('broadcastMessageRead');
        
    }
    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}