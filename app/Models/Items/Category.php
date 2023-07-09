<?php

namespace App\Models\Items;

use Illuminate\Http\Request;
use App\Models\BaseModel;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Category extends BaseModel implements Sortable
{
    use SortableTrait;

    protected $table = 'categories';

    protected $guarded = [ 'category_id' ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $primaryKey = 'category_id';
    public $timestamps = true;



    private function rules()
    {
        if ($this->category_id) {
            // validation rules for updated countries
            return [];
        }

        return [
            'category_name'         => 'required|string|max:150',
        ];
    }

    private function validate($request)
    {

        $validator = \Validator::make($request->all(), $this->rules());

        if ($this->country_id) {
        } else {
            if ($validator->fails()) {
                $this->errors = $validator->errors()->first();
                return false;
            }
        }

        return true;
    }

    public function getCollection(Request $r)
    {
        $this->setLpo($r);
        $this->fields = ['a.*'];

        $this->query = static::from($this->table . ' as a');
        // apply filters here

        if ($r->q) {
            $this->query->where('category_name', 'like', '%' . $r->q . '%');
        }

        if ($r->return_total) {
            $this->total = $this->query->count();
        }

        $this->assignLpo();

        if ($r->return_builder) {
            return $this->query;
        }

        if ($r->paginate) {
            return $this->query->paginate();
        }

        return $this->query->get($this->fields);
    }

    public function store(Request $r)
    {
        if (!$this->validate($r)) {
            return false;
        }

        $this->fill($r->all());

        $category = Category::find($this->category_id);

        if (!$category) {
        } else {
            $this->exists = true;
        }

        try {
            $this->save();
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }

        return $this;
    }

    public function items()
    {
        return $this->belongsTo(Item::class, 'category_id', 'category_id');
    }
}
