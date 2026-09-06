<thead>
    <tr>
      <th scope="col">{{__('module')}}/{{__('sub-module')}}</th>
      <th scope="col">{{__('permissions')}}</th>
    </tr>
</thead>
<tbody>
    @foreach($permissions as $permission)
        @if (!in_array($permission->attribute, ['apikeys', 'utility']))
            <tr>
                <td><span class="text-capitalize">{{__($permission->attribute)}}</span></td>
                <td>
                    @foreach($permission->keywords as $key=>$keyword)
                        @unless (in_array($keyword, [
                            'utility', 'plan.limitation', 'mobile.app', 'user_delete', 'role_delete',
                            'hero.destroy', 'permission_delete', 'merchant_delete', 'deliveryman_delete', 'income_delete',
                            'expense_delete', 'withdraw_reject', 'fund_transfer_delete', 'branch_delete',
                            'email_template_delete', 'third_party_delete', 'language_delete', 'payment_method_delete',
                            'country_delete', 'currency_delete', 'merchant_shop_delete', 'notice_delete', 'news_and_event.destroy', 'about.destroy', 'service.destroy',
                            'feature.destroy', 'statistic.destroy', 'partner_logo.destroy', 'testimonial.destroy',
                            'faq.destroy', 'pages.destroy'
                        ]))
                            <div class="custom-control custom-checkbox">
                                @if($keyword != "")
                                <label class="custom-control-label" for="{{$keyword}}">
                                    <input type="checkbox" class="custom-control-input read common-key" name="permissions[]" value="{{$keyword}}" id="{{$keyword}}" {{in_array($keyword, $role_permissions) ? 'checked':''}}>
                                    <span>{{__($key)}}</span>
                                </label>
                                @endif
                            </div>
                        @endunless
                    @endforeach
                </td>
            </tr>
        @endif
    @endforeach
</tbody>
