<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movie;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            [
                'title' => 'The Shawshank Redemption',
                'genre' => 'Drama',
                'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                'poster_url' => 'images/posters/shawshanak.jpeg',
                'duration' => '2h 22m',
                'language' => 'English',
                'rating' => 9.3,
            ],
            [
                'title' => 'The Godfather',
                'genre' => 'Crime, Drama',
                'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                'poster_url' => 'images/posters/godfather.jpg',
                'duration' => '2h 55m',
                'language' => 'English',
                'rating' => 9.2,
            ],
            [
                'title' => 'The Dark Knight',
                'genre' => 'Action, Crime, Drama',
                'description' => 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.',
                'poster_url' => 'images/posters/darknight.jpg',
                'duration' => '2h 32m',
                'language' => 'English',
                'rating' => 9.0,
            ],
            [
                'title' => 'Pulp Fiction',
                'genre' => 'Crime, Drama',
                'description' => 'The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                'poster_url' => 'images/posters/pulpfiction.jpeg',
                'duration' => '2h 54m',
                'language' => 'English',
                'rating' => 8.9,
            ],
             [
                'title' => 'Forrest Gump',
                'genre' => 'Drama, Romance',
                'description' => 'The presidencies of Kennedy and Johnson, the events of the Vietnam War, the Watergate scandal and other historical events unfold from the perspective of an Alabama man with an IQ of 75, whose only desire was to be reunited with his childhood sweetheart.',
                'poster_url' => 'images/posters/forrestgump.jpeg',
                'duration' => '2h 22m',
                'language' => 'English',
                'rating' => 8.8,
            ]
        ];

        foreach ($movies as $movieData) {
            Movie::create($movieData);
        }
    }
} 