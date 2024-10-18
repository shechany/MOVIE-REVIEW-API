<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre;
class AddGenre extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Genre::create(
            [
            'genre' => 'Adventure',
        ]);
        Genre::create(
            [
            'genre' => 'Action',
        ]);
        Genre::create(
            [
            'genre' => 'Comedy',
        ]);
        Genre::create(
            [
            'genre' => 'Fantasy',
        ]);
        Genre::create(
            [
            'genre' => 'Historical',
        ]);
        Genre::create(
            [
            'genre' => 'Horror',
        ]);
        Genre::create(
            [
            'genre' => 'Romance',
        ]);
        Genre::create(
            [
            'genre' => 'Thriller',
        ]);
        Genre::create(
            [
            'genre' => 'Adventure',
        ]);
        Genre::create(
            [
            'genre' => 'Animation',
        ]);
        Genre::create(
            [
            'genre' => 'Drama',
        ]);
    }
}
