    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('app_name');
                $table->string('app_description');
                $table->string('province');
                $table->string('city');
                $table->string('district');
                $table->string('subdistrict');
                $table->string('postal_code');
                $table->string('address');
                $table->string('phone_number');
                $table->string('email');
                $table->string('logo');
                $table->string('slider_1');
                $table->string('slider_2');
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('settings');
        }
    };
