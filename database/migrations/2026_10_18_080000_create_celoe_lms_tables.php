<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'nomor_induk')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nomor_induk', 30)->nullable()->after('email');
            });
        }

        // Kolom role dipakai untuk membedakan hak akses dosen dan mahasiswa
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->default('mahasiswa')->after('nomor_induk');
            });
        }

        // TODO 2: Kolom tabel mata kuliah
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mk', 20)->unique();
            $table->string('nama_mk');
            $table->text('deskripsi')->nullable();
            $table->unsignedTinyInteger('sks')->default(3);
            $table->string('semester', 50);
            $table->foreignId('dosen_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Tabel Enrollment Mahasiswa (dengan status persetujuan dosen)
        Schema::create('course_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('pending'); // pending | approved | rejected
            $table->unique(['course_id', 'user_id']);
            $table->timestamps();
        });

        // Tabel Materi Kuliah
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('judul');
            $table->longText('konten');
            $table->unsignedTinyInteger('minggu_ke')->default(1);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Tabel Tugas Kuliah
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->dateTime('deadline');
            $table->timestamps();
        });

        // Tabel Pengumpulan Jawaban Teks Mahasiswa
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->longText('jawaban');
            $table->dateTime('submitted_at');
            $table->unique(['assignment_id', 'user_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('course_user');
        Schema::dropIfExists('courses');

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        if (Schema::hasColumn('users', 'nomor_induk')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('nomor_induk');
            });
        }
    }
};