<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'learning_path_id', 'hackathon_team_id', 'placement',
        'type', 'certificate_id', 'file_path', 'qr_code', 'issued_at',
    ];

    protected $casts = ['issued_at' => 'datetime'];

    public function user()          { return $this->belongsTo(User::class); }
    public function learningPath()  { return $this->belongsTo(LearningPath::class); }
    public function hackathonTeam() { return $this->belongsTo(HackathonTeam::class, 'hackathon_team_id'); }
}
