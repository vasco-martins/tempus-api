<?php

namespace App\Http\Livewire\User;

use App\Http\Traits\WithModal;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithModal;

    public $user;
    public $searchTerm = '';

    protected $paginationTheme = 'bootstrap';

    protected $searchColumns = ['id','name','email',];

    protected $listeners = ['update', 'openDestroy', 'destroy'];

	 public $name;
	 public $email;
	 public $password;

    public function render()
    {
        $searchTerm = "%$this->searchTerm%";

        $users = User::query();

        if (strlen($this->searchTerm) > 0) {
            $users = $users->where(function ($query) use ($searchTerm) {
                foreach ($this->searchColumns as $column) {
                    $query->orwhere($column, 'like', $searchTerm);
                }
            });
        }

        $users = $users->latest()->paginate(8);

        return view('livewire.users.index', ['users' => $users]);
    }

    public function create()
    {
        $this->user = null;
        $this->resetErrorBag();
        $this->emit('openModal');
        $this->resetInputs();
    }

    public function update(User $user)
    {
        $this->user = $user;
        $this->resetErrorBag();
        $this->emit('openModal');
        $this->resetInputs($user);
    }

    public function openDestroy(User $user)
    {
        $this->user = $user;
        $this->emit('openDestroyModal');
    }

    public function destroy()
    {
        $this->user->delete();
        $this->emit('toast-success', 'User removida(o) com sucesso');
        $this->emit('closeDestroyModal');
    }

    public function closeModal()
    {
        $this->emit('closeModal');
    }

    public function store()
    {
        $data = $this->validate($this->rules());

        isset($this->user)
            ? $this->user->update($data)
            : User::create($data);

        $this->closeModal();
        $this->emit('toast-success', isset($this->user)
            ? 'User atualizada(o)'
            : 'User criada(o)');
    }

    private function resetInputs(User $user = null)
    {

			$this->name = $user->name ??'';
			$this->email = $user->email ??'';
			$this->password = '';
		if($user) {
			}
        $this->emit('clear-input');
    }

    public function rules()
    {
        return [
            'name' => 'string|required|min:3',
			'email' => 'email|required',
			'password' => '' . $this->user == null ? 'required' : 'nullable' . '',
        ];
    }

}
