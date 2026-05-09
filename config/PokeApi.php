<?php
class PokeApi {
    public static function getPokemonData($id) {
        $url = "https://pokeapi.co/api/v2/pokemon/" . $id;
        try {
            $data = @file_get_contents($url);
            if ($data === false) return null;
            $pokemon = json_decode($data);
            
            // Extraemos los tipos (un Pokémon puede tener uno o dos)
            $tipos = [];
            foreach ($pokemon->types as $t) {
                $tipos[] = $t->type->name;
            }

            return [
                'nombre' => $pokemon->name,
                'imagen' => $pokemon->sprites->other->{'official-artwork'}->front_default,
                'tipos'  => $tipos // Array de strings: ['fire', 'flying']
            ];
        } catch (Exception $e) {
            return null;
        }
    }
}