<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyStudentScore extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.nota_aluno';

    /**
     * @var array
     */
    protected $fillable = [
        'matricula_id'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(LegacyRegistration::class, 'matricula_id');
    }

    /**
     * @return HasMany
     */
    public function scoreGeneral(): HasMany
    {
        return $this->hasMany(LegacyGeneralScore::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany
     */
    public function scoreByDiscipline(): HasMany
    {
        return $this->hasMany(LegacyDisciplineScore::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany
     */
    public function averageByDiscipline(): HasMany
    {
        return $this->hasMany(LegacyDisciplineScoreAverage::class, 'nota_aluno_id');
    }

    /**
     * @return HasMany
     */
    public function averageGeneral(): HasMany
    {
        return $this->hasMany(LegacyGeneralAverage::class, 'nota_aluno_id');
    }
}
