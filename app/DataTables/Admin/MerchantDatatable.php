<?php

namespace App\DataTables\Admin;

use App\Models\Merchant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;

class MerchantDatatable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('options', function ($merchant) {
                return view('admin.merchants.column.actions', compact('merchant'));
            })->addColumn('company_staff', function ($merchant) {
                return view('admin.merchants.column.staff', compact('merchant'));
            })->addColumn('payment', function ($merchant) {
                return view('admin.merchants.column.unpaid_amount', compact('merchant'));
            })->addColumn('parcels', function ($merchant) {
                return view('admin.merchants.column.parcels', compact('merchant'));
            })->addColumn('status', function ($merchant) {
                return view('admin.merchants.column.status', compact('merchant'));
            })->addColumn('website', function ($merchant) {
                return view('admin.merchants.column.website', compact('merchant'));
            })->setRowId('id');
    }

    public function query(Merchant $model): QueryBuilder
    {

        $query = $model->with('parcels', 'defaultAccount.paymentAccount')
            ->when(!hasPermission('read_all_merchant'), function ($query) {
                $query->whereHas('shops', function ($q) {
                    $q->where('pickup_branch_id', \Sentinel::getUser()->branch_id);
                });
            })
            ->when($this->request->branch, function ($query) {
                $branch = $this->request->branch;
                $query->whereHas('shops', function ($q) use ($branch) {
                    $q->when($branch == 'pending', function ($search) {
                        $search->whereNull('branch_id');
                    })->when($branch != 'pending', function ($search) use ($branch) {
                        $search->where('pickup_branch_id', $branch);
                    });
                });
            })

            ->when($this->request->company_name, function ($query, $search) {
                $query->where('company', 'like', "%$search%");
            })

            ->when($this->request->has('approval_status') && ($this->request->approval_status !== "" && $this->request->approval_status != null), function ($query) {
                $approve = $this->request->approval_status;
                dd($approve==null);
                $query->where('registration_confirmed', $approve);
            })

            ->when($this->request->has('sort_by') && ($this->request->sort_by !== "" && $this->request->sort_by != null), function ($query) {
                $sort_by = $this->request->sort_by;

                if ($sort_by == 'oldest_on_top') {
                    $query->orderBy('created_at', 'asc');
                } elseif (($sort_by == 'newest_on_top')) {
                    $query->orderBy('created_at', 'desc');
                } else {
                    $query->orderBy('created_at', 'desc');
                }
            })

            ->when($this->request->has('status') && ($this->request->status !== "" && $this->request->status != null), function ($query) {
                $status = $this->request->status;
                $query->where('status', $status);
            })


            ->when(request('search')['value'] ?? false, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('company', 'like', "%$search%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%")
                                ->orWhere('phone_number', 'like', "%$search%");
                        });
                });
            });



        return $query->latest();
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:99.8%')
            ->footerCallback('function (row, data, start, end, display ) {
                $(".dataTables_length select").addClass("form-select form-select-lg without_search mb-3");
                selectionFields();
            }')
            ->parameters([
                'dom'        => 'Blfrtip',
                'buttons'    => [
                    [],
                ],
                'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                'language'   => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu'        => '_MENU_ ' . __('merchant_per_page'),
                    'search'            => '',
                ],
            ]);
    }


    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->addClass('text-center')->title(__('options')),
            Column::computed('company_staff')->title(__('merchant')),
            Column::computed('payment')->title(__('payment')),
            Column::computed('parcels')->title(__('parcels')),
            Column::computed('status')->title(__('status'))->exportable(false),
            Column::computed('website')->title(__('website'))
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'merchant' . date('YmdHis');
    }
}
