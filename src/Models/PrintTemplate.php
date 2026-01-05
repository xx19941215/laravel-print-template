<?php

namespace Xx19941215\PrintTemplate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class PrintTemplate extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'print_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'org_id',
        'assoc_type',
        'assoc_id',
        'code',
        'name',
        'config',
        'creator_id',
        'modifier_id',
        'modified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'config' => 'array',
        'modified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * 获取模板code属性
     *
     * @return string
     */
    public static function genCode($org_id)
    {
        // 找出当前 org_id 下 code 最大的供应商编号（数字部分）
        $latest = PrintTemplate::query()
            ->where('org_id', $org_id)
            ->where('code', 'like', 'DY%')
            ->orderByDesc('code')
            ->select('code')
            ->first();

        // 提取数字部分，转为 int，+1
        $number = 1;
        if ($latest && preg_match('/DY(\d{4})/', $latest->code, $matches)) {
            $number = intval($matches[1]) + 1;
        }

        return 'DY' . sprintf('%04d', $number);
    }


    /**
     * 获取创建者用户模型
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        $userModel = Config::get('print_template.user_model', 'App\\Models\\Admin');
        return $this->belongsTo($userModel, 'creator_id');
    }

    /**
     * 获取修改者用户模型
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function modifier()
    {
        $userModel = Config::get('print_template.user_model', 'App\\Models\\Admin');
        return $this->belongsTo($userModel, 'modifier_id');
    }
}