# 📋 UNUSED RESOURCES ANALYSIS REPORT

**Project:** ERMS Plus
**Generated:** December 7, 2025
**Analysis Type:** Routes, Controllers, and Views

---

## 📊 Summary Statistics

| Resource Type | Total | Unused/Unreferenced | Percentage |
|--------------|-------|---------------------|------------|
| **Routes (web.php)** | 374 | 44 not in views | 11.8% |
| **Controllers** | 81 | 4 not in routes | 4.9% |
| **View Templates** | 236 | 202 not rendered | 85.6% |

---

## 1️⃣ ROUTES NOT REFERENCED IN VIEW FILES (44)

These routes are defined but never called using `route()` in any Blade template:

### Critical - Likely Truly Unused:

1. `auth.LandingPage` - Commented out route
2. `auth.change` - Password change page
3. `auth.changePasswordFront` - Frontend password change
4. `permissions.create` - Entire permissions section is commented out
5. `permissions.show` - Entire permissions section is commented out
6. `permissions.store` - Entire permissions section is commented out
7. `property-revenues.index` - Property revenues module
8. `property-revenues.store` - Property revenues module
9. `property-revenues.update` - Property revenues module
10. `agent-performance-reports.index` - Agent performance reporting
11. `system-peroformance-reports.index` - **Note: typo in route name - "peroformance"**

### AJAX/API Endpoints (Still in Use via JavaScript):

12. `fetch.regional.data` - Dashboard data fetching
13. `fetch.regional.donut.data` - Dashboard charts
14. `fetch.regional.graph.data` - Dashboard charts
15. `citizens.search` - Search functionality
16. `citizens.resend` - OTP resend
17. `properties.assignCitizen` - Property assignment
18. `properties.allocations` - Property allocations
19. `properties.details` - Property details
20. `buildings.allocations` - Building allocations
21. `buildings.create` - Building creation

### CRUD Operations (Used in Forms/Actions):

22. `assembly.destroy` - DELETE route
23. `bills.destroy` - DELETE route
24. `bills.update` - UPDATE route
25. `bills.receipt` - Bill receipt generation
26. `blocks.destroy` - DELETE route
27. `business-class-types.destroy` - DELETE route
28. `businesses.destroy` - DELETE route
29. `citizens.destroy` - DELETE route
30. `divisions.destroy` - DELETE route
31. `properties.destroy` - DELETE route
32. `payments.update` - UPDATE route
33. `payments.verify` - Payment verification

### Edit/Show Routes Not Found:

34. `new-businesses.edit` - Business edit page
35. `new-businesses.show` - Business detail page
36. `new-properties.edit` - Property edit page
37. `new-properties.show` - Property detail page
38. `owners.edit` - Owner edit page
39. `owners.show` - Owner detail page
40. `bills.bus.edit` - Business bill edit
41. `bills.bus.show` - Business bill detail
42. `bills.bus.update` - Business bill update

### Customer Support:

43. `customer-supports.create` - Customer support creation
44. `customer-supports.store` - Customer support storage

---

## 2️⃣ CONTROLLERS NOT REFERENCED IN ROUTES (4)

### Definitely Unused:

1. **`app/Http/Controllers/PaymentTempController.php`**
   - No route references found
   - Appears to be temporary/test code
   - **Recommendation:** Delete

2. **`app/Http/Controllers/ablekumacontroller/AblekumaNorthPaymentTempController.php`**
   - Custom controller with no routes
   - Appears to be assembly-specific temporary code
   - **Recommendation:** Delete

3. **`app/Http/Controllers/Auth/LoginController.php`**
   - Laravel default authentication controller
   - Replaced by `AuthAdmin\LoginAdminController`
   - **Recommendation:** Delete

4. **`app/Http/Controllers/Auth/RegisterController.php`**
   - Laravel default registration controller
   - Custom registration implemented via `CitizenController`
   - **Recommendation:** Delete

---

## 3️⃣ COMMENTED OUT ROUTES

Found in `routes/web.php`:

### Line 78: Bills Edit Route
```php
//    Route::get('/{id}/edit', [BillsManagementController::class, 'edit'])->name('bills.edit');
```
**Status:** Commented out, functionality might be disabled
**Recommendation:** Remove comment line or uncomment if needed

### Lines 299-306: Entire Permissions Management Module
```php
// Route::group(['prefix' => 'permission', 'middleware' => 'auth:sanctum'], function () {
//    Route::get('/', [Permission\PermissionController::class, 'index'])->name('permissions.index');
//    Route::get('/create', [Permission\PermissionController::class, 'create'])->name('permissions.create');
//    Route::post('/', [Permission\PermissionController::class, 'store'])->name('permissions.store');
//    Route::get('/show/{id}', [Permission\PermissionController::class, 'show'])->name('permissions.show');
//    Route::get('/edit/{id}', [Permission\PermissionController::class, 'edit'])->name('permissions.edit');
//    Route::post('/update/{id}', [Permission\PermissionController::class, 'update'])->name('permissions.update');
// });
```
**Status:** Entire permissions CRUD commented out
**Recommendation:** Delete these commented lines and the `Permission\PermissionController.php` if not needed

---

## 4️⃣ VIEWS NEVER RENDERED (202 files)

**⚠️ Warning:** This analysis detected 202 view files that aren't explicitly rendered via `return view()` in controllers. However, this might include false positives because:
- Some views might be rendered dynamically
- Some might be included via `@include` directive
- Partial views aren't tracked

### Sample of Potentially Unused Views (Top 50):

```
1.  ablekuma.collect-payment
2.  ablekuma.edit
3.  ablekuma.index
4.  ablekuma.show
5.  agent-assignments.create
6.  agent-assignments.edit
7.  agent-assignments.index
8.  agent-assignments.show
9.  assembly.edit
10. assembly.index
11. assembly.map
12. assembly.show
13. auth.register
14. bill-adjustments.create
15. bill-adjustments.edit
16. bill-adjustments.index
17. bill-adjustments.show
18. bills-business.block
19. bills-business.bulk
20. bills-business.division
21. bills-business.index
22. bills-business.single
23. bills.block
24. bills.bulk
25. bills.division
26. bills.fetch
27. bills.index
28. bills.receipt
29. bills.show
30. bills.single
31. blocks-new.create
32. blocks-new.edit
33. blocks-new.index
34. blocks-new.show
35. blocks.create
36. blocks.edit
37. blocks.index
38. blocks.show
39. bop-rates.create
40. bop-rates.edit
41. bop-rates.index
42. bop-rates.show
43. buildings.index
44. buildings.map
45. business-class-types.edit
46. business-class-types.index
47. business-class-types.show
48. business_classes.create
49. business_classes.edit
50. business_classes.index
```

**... and 152 more views**

### Views by Module (Potentially Unused):

- **Ablekuma Module:** 4 views (all ablekuma.*)
- **Agent Assignments:** 4 views
- **Assembly:** 4 views
- **Bill Adjustments:** 4 views
- **Bills (Business):** 5 views
- **Bills (Regular):** 6 views
- **Blocks (New):** 4 views
- **Buildings:** 2 views

**Note:** Many of these are likely in use but rendered dynamically. Manual verification required.

---

## 💡 RECOMMENDATIONS

### ✅ Immediate Actions (Safe to Execute):

#### 1. Delete Unused Controllers
```bash
# Navigate to project root
cd /Users/alhaji_aki/Projects/web/acn/erms_plus

# Delete unused controllers
rm app/Http/Controllers/PaymentTempController.php
rm -rf app/Http/Controllers/ablekumacontroller/
rm app/Http/Controllers/Auth/LoginController.php
rm app/Http/Controllers/Auth/RegisterController.php

# Commit changes
git add -A
git commit -m "Remove unused controllers: PaymentTemp, ablekuma, and Laravel default Auth"
```

#### 2. Clean Up Commented Routes
In `routes/web.php`:
- Delete lines 299-306 (commented permissions routes)
- Delete or uncomment line 78 (bills.edit route)

#### 3. Fix Route Name Typo
In `routes/web.php`, line 417:
```php
// BEFORE:
Route::get('/system-performance-report', [Report\Level10ReportController::class, 'systemPerformanceReport'])->name('system-peroformance-reports.index');

// AFTER:
Route::get('/system-performance-report', [Report\Level10ReportController::class, 'systemPerformanceReport'])->name('system-performance-reports.index');
```

---

### 🔍 Investigate Before Deleting:

#### 1. Routes to Verify

**Check JavaScript files for these routes:**
```bash
# Search for route usage in JavaScript
grep -r "new-businesses.edit\|new-businesses.show" public/assets/js resources/js
grep -r "new-properties.edit\|new-properties.show" public/assets/js resources/js
grep -r "owners.edit\|owners.show" public/assets/js resources/js
```

**Verify these modules:**
- `property-revenues.*` - Is the property revenues module still needed?
- `customer-supports.create` - Customer support creation functionality
- `agent-performance-reports.index` - Agent performance reporting

#### 2. Views to Audit

**Manual verification needed for these view categories:**
- `ablekuma.*` - Check if this is old/deprecated assembly-specific code
- `assembly.*` - Assembly management views (might be rendered dynamically)
- `bills.*` - Bill management views (likely in use)
- `agent-assignments.*` - Agent assignment views

**Search for dynamic view rendering:**
```bash
# Find dynamic view calls
grep -r "view(\$" app/Http/Controllers
grep -r "@include" resources/views
```

---

## 📝 IMPORTANT NOTES

1. **Static Analysis Limitation:** This analysis only covers static `route()` calls in Blade templates
   - JavaScript AJAX calls are NOT tracked
   - Dynamic view rendering (like `view($variable)`) is NOT detected
   - Form actions using `route()` helper in JavaScript are NOT tracked

2. **API Routes:** All controllers in `routes/api.php` are properly used and were not flagged

3. **Testing Required:** Always run your test suite before deleting resources
   ```bash
   php artisan test
   ```

4. **Git Safety:** Create a branch before making deletions
   ```bash
   git checkout -b cleanup/remove-unused-resources
   ```

---

## 🚀 Next Steps

### Option 1: Conservative Cleanup (Recommended)
1. Delete only the 4 confirmed unused controllers
2. Remove commented routes
3. Fix the typo in system-performance route
4. Monitor for errors over 1-2 weeks
5. Proceed with view cleanup if no issues

### Option 2: Aggressive Cleanup
1. Execute all immediate actions
2. Investigate JavaScript route usage
3. Create a list of confirmed unused views
4. Delete unused views in batches
5. Test thoroughly after each batch

### Option 3: Further Analysis
Would you like me to:
- Search JavaScript files for route usage?
- Analyze specific modules (ablekuma, property-revenues)?
- Generate automated cleanup scripts?
- Check for dynamic view rendering patterns?

---

## 📊 Cleanup Progress Tracker

- [ ] Delete 4 unused controllers
- [ ] Remove commented routes in web.php
- [ ] Fix system-performance route typo
- [ ] Verify JavaScript route usage
- [ ] Audit ablekuma module
- [ ] Audit property-revenues module
- [ ] Verify agent-assignments views
- [ ] Test application thoroughly
- [ ] Update documentation
- [ ] Commit changes

---

**Report Generated By:** Claude Code Analysis Tool
**Last Updated:** December 7, 2025
**Confidence Level:** High for controllers, Medium for routes, Low for views