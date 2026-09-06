@extends('backend.layouts.master')
@section('title')
    {{ __('edit') }} {{ __('role') }}
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row gx-20">
            <div class="col-lg-12">
                <div class="header-top d-flex justify-content-between align-items-center mb-12">
                    <h3 class="section-title">{{ __('edit') }} {{ __('role') }}</h3>
                    <div class="oftions-content-right">
                        <a href="{{ url()->previous() }}" class="d-flex align-items-center btn sg-btn-primary gap-2"><i
                                class="icon las la-arrow-left"></i><span>{{ __('back') }}</span></a>
                    </div>
                </div>
                <form action="{{ route('roles.update', $role->id) }}" class="form-validate" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white redious-border p-20 p-sm-30">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card-inner">
                                    <div class="row g-gs">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-full-name">{{ __('name') }} <span
                                                        class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="fv-full-name"
                                                        value="{{ $role->name }}" name="name">
                                                @if ($errors->has('name'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('name') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="fv-email">{{ __('slug') }}</label>
                                                <div >
                                                    <input type="text" class="form-control" id="fv-email"
                                                        value="{{ $role->slug }}" name="slug">
                                                </div>
                                                @if ($errors->has('slug'))
                                                    <div class="invalid-feedback help-block">
                                                        <p>{{ $errors->first('slug') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-inner ">
                                    <table class="table role-create-table role-permission">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ __('module') }}/{{ __('sub-module') }}</th>
                                                <th scope="col">{{ __('permissions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($permissions as $permission)
                                                <tr>
                                                    <td><span
                                                            class="text-capitalize">{{ __($permission->attribute) }}</span>
                                                    </td>

                                                    <td>
                                                        @foreach ($permission->keywords as $key => $keyword)
                                                            <div class="custom-control custom-checkbox">
                                                                @if ($keyword != '')
                                                                    <label class="custom-control-label"
                                                                        for="{{ $keyword }}">
                                                                        <input type="checkbox"
                                                                            class="custom-control-input read common-key"
                                                                            name="permissions[]"
                                                                            value="{{ $keyword }}"
                                                                            id="{{ $keyword }}"
                                                                            {{ in_array($keyword, $role->permissions) ? 'checked' : '' }}>
                                                                        <span>{{ __($key) }}</span>
                                                                    </label>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-md-12 text-right mt-4">
                                            <div class="mb-3">
                                                <button type="submit"  class="btn sg-btn-primary resubmit">{{ __('update') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('admin.roles.script')
@endsection
