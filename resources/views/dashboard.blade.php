<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight ">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>
                <?php
                $users=App\Models\User::whereNotNull('last_seen')
                ->orderBy('last_seen','desc')
                ->get();
                ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>id</th>   
                         <th>name</th>
                         <th>last seen</th>
                         <th>status</th>   
                        </tr>
                    </thead>
                    <tbody>
                        @if($users->isNotEmpty())
                       @foreach($users as $user)
                       <tr>
                        <td>{{$user->id}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{\Carbon\Carbon::parse($user->last_seen)->diffForHumans()}}</td>
                        <td>
                         @if(Cache::has('user-is-online-'.$user->id))
                         <span class="text-center"><font color="green">Online</font></span>
                         @else
                         <span class="text-center"><font color="red">Offline</font></span>
                         @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif  
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
