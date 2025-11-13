@extends('layouts.app')

@section('title', 'CRM - Novo Cliente')

@section('content')
    <div class="w-full max-w-2xl mx-auto bg-white rounded-3xl shadow-2xl p-10 border border-gray-200">
        <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-10">Cadastrar Novo Cliente</h2>

        {{-- Mensagens --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl mb-6 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl mb-6 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('clients.store') }}" class="space-y-6">
            @csrf

            {{-- CPF e Nome --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">CPF</label>
                    <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}" required
                           class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3 shadow-sm">
                    @error('cpf')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nome</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3 shadow-sm">
                    @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Email e Telefone --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3 shadow-sm">
                    @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Telefone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                           class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3 shadow-sm">
                    @error('phone')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Endereço, Cidade, Estado --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Endereço</label>
                <input type="text" name="address" value="{{ old('address') }}" required
                       class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3 shadow-sm">
                @error('address')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cidade</label>
                    <input type="text" name="city" value="{{ old('city') }}" required
                           class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3 shadow-sm">
                    @error('city')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                    <select name="state" required
                            class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3 text-gray-800 shadow-sm">
                        <option value="">Selecione o Estado</option>
                        @foreach ([
                            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
                            'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                            'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
                            'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                            'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
                            'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                            'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
                        ] as $uf => $estado)
                            <option value="{{ $uf }}" {{ old('state') == $uf ? 'selected' : '' }}>{{ $estado }}</option>
                        @endforeach
                    </select>
                    @error('state')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Redes Sociais --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Redes Sociais</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                    <select id="social-network-select" name="social_networks"
                            class="border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-3 py-2 shadow-sm w-full">
                        <option value="">Rede social</option>
                        @foreach($social_networks as $social_network)
                            <option value="{{ $social_network->id }}" {{ old('$social_network_id') == $social_network->id ? 'selected' : '' }}>
                                {{ $social_network->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="url" id="social-network-url" placeholder="URL da rede social"
                           class="border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-3 py-2 shadow-sm w-full">
                    <button type="button" id="add-social-network" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded-xl shadow-lg transition cursor-pointer">
                        + Adicionar
                    </button>
                </div>

                <ul id="social-network-list" class="space-y-2">
                </ul>
            </div>

            {{-- Origem do Contato --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Origem do Contato</label>
                <select name="contact_source" required
                        class="w-full border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl px-5 py-3 text-gray-800 shadow-sm">
                    <option value="">Selecione a origem</option>
                    @foreach($contact_sources as $contact_source)
                        <option value="{{ $contact_source->id }}" {{ old('role_id') == $contact_source->id ? 'selected' : '' }}>
                            {{ $contact_source->description }}
                        </option>
                    @endforeach
                </select>
                @error('contact_source')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg transition transform hover:scale-[1.02] cursor-pointer">
                Cadastrar Cliente
            </button>
        </form>
    </div>

    {{-- Scripts de Máscara --}}
    <script>
        // Máscara CPF: 000.000.000-00
        const cpfInput = document.getElementById('cpf');
        cpfInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);

            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });

        // Máscara Telefone: (00) 00000-0000
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);

            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
            e.target.value = value;
        });

        // Redes Sociais Dinâmicas
        const addSocialBtn = document.getElementById('add-social-network');
        const socialSelect = document.getElementById('social-network-select');
        const socialUrl = document.getElementById('social-network-url');
        const socialList = document.getElementById('social-network-list');

        let index = 0;

        addSocialBtn.addEventListener('click', () => {
            const networkId = socialSelect.value;
            const networkName = socialSelect.options[socialSelect.selectedIndex].text;
            const url = socialUrl.value.trim();

            if (networkName === '' || url === '') return;

            // Cria elemento da lista
            const li = document.createElement('li');
            li.className = "flex items-center justify-between bg-gray-100 px-4 py-2 rounded-xl shadow-sm";

            li.innerHTML = `
            <span>${networkName}: <a href="${url}" target="_blank" class="text-indigo-600 hover:underline">${url}</a></span>
            <button type="button" class="text-red-600 font-bold px-2 py-1 hover:bg-red-100 rounded" onclick="this.parentElement.remove()">X</button>
            <input type="hidden" name="social_networks[${index}][id]" value="${networkId}">
            <input type="hidden" name="social_networks[${index}][url]" value="${url}">
            `;

            socialList.appendChild(li);

            // Limpa campos
            socialSelect.value = '';
            socialUrl.value = '';

            index++;
        });
        </script>
@endsection
