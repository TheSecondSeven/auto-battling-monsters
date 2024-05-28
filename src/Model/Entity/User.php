<?php
namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher; // Add this line
use Cake\ORM\Entity;

class User extends Entity
{
    // Code from bake.

    // Add this method
    protected function _setPassword(string $password) : ?string
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher())->hash($password);
        }
    }
    function getRegistrationAnswerValues() {
        return [
            'Ice Cream' => [
                'Water'
            ],
            'Crepe' => [
                'Electric'
            ],
            'Jello' => [
                'Earth',
                'Poison'
            ],
            'Strawberry Pie' => [
                'Fire',
                'Earth'
            ],
            'Chocolate Cake' => [
                'Undead'
            ],
            'S\'more' => [
                'Fire',
                'Fighting',
                'Undead'
            ],
            'Cookie' => [
                'Fire'
            ],
            'Banana Pudding' => [
                'Earth',
                'Electric'
            ],
            'Red' => [
                'Fire'
            ],
            'Black' => [
                'Undead'
            ],
            'Yellow' => [
                'Electric'
            ],
            'Pink' => [
                'Fighting'
            ],
            'Blue' => [
                'Water'
            ],
            'Green' => [
                'Earth'
            ],
            'White' => [
                'Poison'
            ],
            'Doug' => [
                'Earth'
            ],
            'Animaniacs' => [
                'Electric'
            ],
            'The Simpsons' => [
                'Undead'
            ],
            'Beavis and Butt-Head' => [
                'Fighting'
            ],
            'Rugrats' => [
                'Water'
            ],
            'Pinky and the Brain' => [
                'Poison'
            ],
            'The Magic School Bus' => [
                'Electric'
            ],
            'Gargoyles' => [
                'Undead'
            ],
            'Daria' => [
                'Undead'
            ],
            'Hey Arnold!' => [
                'Fire'
            ],
            'Darkwing Duck' => [
                'Poison',
                'Fighting',
                'Undead'
            ],
            'Rocko\'s Modern Life' => [
                'Fire',
                'Earth',
                'Electric',
            ],
            'The Ren & Stimpy Show' => [
                'Fire',
                'Electric',
                'Poison',
                'Fighting'
            ],
            'Extra Cheese' => [
                'Water'
            ],
            'Pepperoni' => [
                'Fire',
                'Fighting'
            ],
            'Olives' => [
                'Water',
                'Undead'
            ],
            'Bell Peppers' => [
                'Water',
                'Earth'
            ],
            'Sausage' => [
                'Earth',
                'Undead'
            ],
            'Ham' => [
                'Fighting'
            ],
            'Pineapple' => [
                'Electric'
            ],
            'Jalapeños' => [
                'Fire',
                'Earth'
            ],
            'Mushroom' => [
                'Earth',
                'Poison'
            ],
            'Bacon' => [
                'Undead'
            ],
            'Onions' => [
                'Water'
            ],
            'Monday' => [
                'Fire'
            ],
            'Tuesday' => [
                'Water'
            ],
            'Wednesday' => [
                'Earth'
            ],
            'Thursday' => [
                'Electric'
            ],
            'Friday' => [
                'Poison'
            ],
            'Saturday' => [
                'Fighting'
            ],
            'Sunday' => [
                'Undead'
            ],
            'Barbarian' => [
                'Fire',
                'Fighting'
            ],
            'Bard' => [
                'Electric',
                'Poison',
                'Fighting'
            ],
            'Cleric' => [
                'Fire',
                'Water'
            ],
            'Druid' => [
                'Earth'
            ],
            'Fighter' => [
                'Fighting'
            ],
            'Monk' => [
                'Fire',
                'Water',
                'Earth',
                'Fighting'
            ],
            'Paladin' => [
                'Fire',
                'Fighting',
                'Undead'
            ],
            'Ranger' => [
                'Water',
                'Earth',
                'Poison',
                'Fighting'
            ],
            'Rogue' => [
                'Poison',
                'Fighting'
            ],
            'Sorcerer' => [
                'Fire',
                'Water',
                'Earth',
                'Electric',
                'Undead'
            ],
            'Warlock' => [
                'Fire',
                'Poison',
                'Undead'
            ],
            'Wizard' => [
                'Fire',
                'Water',
                'Earth',
                'Electric'
            ],
            'X-Men' => [
                'Electric',
                'Fire',
                'Fighting',
            ],
            'Spider-man' => [
                'Earth',
                'Fighting'
            ]
        ];
    }
}